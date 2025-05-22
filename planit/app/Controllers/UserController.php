<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;
use App\Models\EventModel;
use App\Models\PraiseModel;
use App\Controllers\AuthController;

/**
 * Clase UserController
 * 
 * Controlador para gestionar las funcionalidades relacionadas con los usuarios, 
 * como la gestión de perfiles, bloqueo/desbloqueo de usuarios, modificación de puntos, 
 * y elogios entre usuarios.
 */
class UserController extends Controller
{
    protected $userModel;
    protected $authController;
    protected $eventModel;
    private $praiseModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->eventModel = new EventModel();
        $this->authController = new AuthController();
        $this->praiseModel = new PraiseModel();
    }

    /**
     * Verifica si hay una sesión activa. Si no, redirige al inicio de sesión.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse|null Redirige al login si no hay sesión activa.
     */
    public function checkSession()
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }
    }

    /**
     * Muestra el perfil de un usuario.
     * 
     * Si no se proporciona un ID, muestra el perfil del usuario en sesión.
     * 
     * @param int|null $id ID del usuario (opcional).
     * @return \CodeIgniter\View\View Vista del perfil del usuario.
     */
    public function profile($id = null)
    {
        $this->checkSession();
        if (isset($id)) {
            $user = $this->userModel->find($id);
        } else {
            $user = $this->userModel->find(session()->get('user_id'));
        }
        if (!$user) {
            session()->remove('user_id');
            return redirect()->to('/login');
        }
        $data = [
            'id' => $user['id'],
            'username' => $user['username'],
            'photo' => $user['photo'] ? base64_encode($user['photo']) : '',
            'location'  => $user['location']
        ];
        return view('user/profile', $data);
    }

    /**
     * Actualiza los datos del perfil de un usuario.
     * 
     * Valida los datos proporcionados y actualiza la información en la base de datos.
     * 
     * @param int $id ID del usuario a actualizar.
     * @return \CodeIgniter\HTTP\RedirectResponse Redirige con un mensaje de éxito o error.
     */
    public function update($id)
    {
        $this->checkSession();
        $user = $this->userModel->find($id);
        if (!$user) {
            session()->remove('user_id');
            return redirect()->to('/login');
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $location = $this->request->getPost('location');
        $passwordConfirm = $this->request->getPost('password_confirm');
        $file = $this->request->getFile('photo');

        if ($password && $password !== $passwordConfirm) {
            return redirect()->to('/profile')->with('error', 'Las contraseñas no coinciden');
        }
        $data = [
            'username' => $username
        ];

        if ($password && $password === $passwordConfirm) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        if ($location) {
            $data['location'] = $location;
        }
        if ($file && $file->isValid()) {
            $data['photo'] = file_get_contents($file->getTempName());
        }
        $this->userModel->update($user, $data);
        return redirect()->to('/profile/' . $id)->with('success', 'Perfil actualizado con éxito');
    }

    /**
     * Muestra la información pública de un usuario.
     * 
     * Incluye eventos creados, nivel de usuario y si ha sido elogiado.
     * 
     * @param string $username Nombre de usuario.
     * @return \CodeIgniter\View\View Vista con la información del usuario.
     */
    public function show($username)
    {
        $user = $this->userModel->where('username', $username)->first();
        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Usuario no encontrado');
        }
        $all_events = $this->eventModel->where('user_id', $user['id'])->findAll();
        $level = $this->getUserLevel($user['points']);

        $hasPraised = false;
        if (session()->get('user_id')) {
            $hasPraised = $this->praiseModel->where('id_praiser', session()->get('user_id'))
                ->where('id_praised', $user['id'])
                ->first() !== null;
        }


        return view('user/user', [
            'username' => $user['username'],
            'photo' => $user['photo'],
            'location' => $user['location'],
            'events' => $all_events,
            'points' => $user['points'],
            'level' => $level,
            'user_id' => $user['id'],
            'hasPraised' => $hasPraised
        ]);
    }

    /**
     * Calcula el nivel de un usuario basado en sus puntos.
     * 
     * @param int $points Puntos del usuario.
     * @return string Nivel del usuario.
     */
    public function getUserLevel($points)
    {
        if ($points >= 999) {
            return 'Usuario Veterano';
        } elseif ($points >= 499) {
            return 'Usuario Experimentado';
        } elseif ($points >= 99) {
            return 'Usuario';
        } else {
            return 'Usuario Nuevo';
        }
    }

    /**
     * Muestra la lista de usuarios bloqueados por el usuario en sesión.
     * 
     * Permite buscar usuarios y ver los bloqueados.
     * 
     * @return \CodeIgniter\View\View Vista con los usuarios bloqueados.
     */
    public function blockedUsers()
    {
        $username = $this->request->getGet('username');
        $this->checkSession();
        $user = $this->userModel->find(session()->get('user_id'));
        $userId = $user['id'];
        $data = [
            'user_id' => $userId,
            'username' => $user['username'],
            'photo' => $user['photo'] ? base64_encode($user['photo']) : '',
        ];


        if (!empty($username)) {
            $data['users'] = $this->userModel
                ->like('username', $username)
                ->where('id !=', $userId)
                ->findAll();

            $data['blocked_ids'] = $this->userModel->db->table('blocked_users')
                ->select('id_blocked')
                ->where('id_user', $userId)
                ->get()
                ->getResultArray();
        } else {
            $data['blocked_users'] = $this->userModel
                ->whereIn('id', function ($builder) use ($userId) {
                    return $builder
                        ->select('id_blocked')
                        ->from('blocked_users')
                        ->where('id_user', $userId);
                })
                ->findAll();
        }

        return view('user/blockedUsers', $data);
    }

    /**
     * Bloquea a un usuario específico.
     * 
     * Elimina al usuario bloqueado de los eventos del usuario en sesión.
     * 
     * @param int $id ID del usuario a bloquear.
     * @return \CodeIgniter\HTTP\RedirectResponse Redirige con un mensaje de éxito o error.
     */
    public function block($id)
    {
        $this->checkSession();

        $userId = session()->get('user_id');

        if ($userId == $id) {
            return redirect()->to('/users/blockedUsers')->with('error', 'No puedes bloquearte a ti mismo');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/users/blockedUsers')->with('error', 'Usuario no encontrado');
        }

        $this->userModel->db->table('blocked_users')->insert([
            'id_user' => $userId,
            'id_blocked' => $id
        ]);
        $this->kickBlockedUser($userId, $id);

        return redirect()->to('/users/blockedUsers')->with('success', 'Usuario bloqueado con éxito');
    }

    /**
     * Elimina a un usuario bloqueado de los eventos del usuario en sesión.
     * 
     * También ajusta los puntos de ambos usuarios.
     * 
     * @param int $userId ID del usuario en sesión.
     * @param int $blockedId ID del usuario bloqueado.
     */
    public function kickBlockedUser($userId, $blockedId)
    {
        $events = $this->eventModel->where('user_id', $userId)->findAll();

        $creatorId = $userId;
        $attendeeId = $blockedId;
        $this->modifyPoints($creatorId, 10, 'subtract');
        $this->modifyPoints($attendeeId, 5, 'subtract');
        foreach ($events as $event) {
            $this->eventModel->db->table('event_user')
                ->where('id_user', $blockedId)
                ->where('id_event', $event['id'])
                ->delete();
        }
    }

    /**
     * Elimina la cuenta del usuario en sesión.
     * 
     * Borra los datos del usuario, sus eventos y bloqueos.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirige al catálogo tras eliminar la cuenta.
     */
    public function delete($id = null)
    {
        if (!isset($id)) {
            $id = session()->get('user_id');
        }
        $this->userModel->delete($id);
        $this->userModel->db->table('event_user')->where('id_user', $id)->delete();
        $this->userModel->db->table('blocked_users')->where('id_user', $id)->delete();
        $this->authController->logout();
        return redirect()->to('/catalog');
    }

    /**
     * Desbloquea a un usuario previamente bloqueado.
     * 
     * @param int $id ID del usuario a desbloquear.
     * @return \CodeIgniter\HTTP\RedirectResponse Redirige con un mensaje de éxito.
     */
    public function unblock($id)
    {
        $this->checkSession();

        $userId = session()->get('user_id');

        $this->userModel->db->table('blocked_users')
            ->where('id_user', $userId)
            ->where('id_blocked', $id)
            ->delete();

        return redirect()->to('/users/blockedUsers')->with('success', 'Usuario desbloqueado con éxito');
    }

    /**
     * Muestra la lista de asistentes a un evento.
     * 
     * Incluye información sobre si el usuario en sesión es el creador del evento.
     * 
     * @param int $event_id ID del evento.
     * @return \CodeIgniter\View\View Vista con los asistentes al evento.
     */
    public function showAttendees($event_id)
    {
        if (session()->get('user_id')) {
            $event = $this->eventModel->find($event_id);
            $user_session_id = session()->get('user_id');
            if ($event['user_id'] == $user_session_id) {
                $data['isCreator'] = true;
            }
        }
        $data['attendees'] = $this->userModel->whereIn('id', $this->userModel->db->table('event_user')->select('id_user')->where('id_event', $event_id))->findAll();
        return view('user/attendees', $data);
    }

    /**
     * Modifica los puntos de un usuario.
     * 
     * Permite sumar o restar puntos según la operación especificada.
     * 
     * @param int $userId ID del usuario.
     * @param int $points Cantidad de puntos a modificar.
     * @param string $operation Operación a realizar ('add' o 'subtract').
     */
    public function modifyPoints($userId, $points, $operation = 'add')
    {
        $user = $this->userModel->find($userId);
        if ($user) {
            if ($operation == 'add') {
                $this->userModel->update($userId, ['points' => $user['points'] + $points]);
            } elseif ($operation == 'subtract') {
                $this->userModel->update($userId, ['points' => $user['points'] - $points]);
            }
        }
    }

    /**
     * Elogia a un usuario específico.
     * 
     * Valida que el usuario en sesión no se elogie a sí mismo y que no haya elogiado previamente al usuario.
     * 
     * @param int $praisedId ID del usuario a elogiar.
     * @return \CodeIgniter\HTTP\RedirectResponse Redirige con un mensaje de éxito o error.
     */
    public function praise($praisedId)
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }

        $praiserId = session()->get('user_id');

        if ($praiserId == $praisedId) {
            return redirect()->back()->with('error', 'No puedes elogiarte a ti mismo');
        }

        $existingPraise = $this->praiseModel->where('id_praiser', $praiserId)
            ->where('id_praised', $praisedId)
            ->first();

        if ($existingPraise) {
            return redirect()->back()->with('error', 'Ya has elogiado a este usuario');
        }

        $this->praiseModel->insert([
            'id_praiser' => $praiserId,
            'id_praised' => $praisedId
        ]);

        $this->modifyPoints($praisedId, 5, 'add');

        return redirect()->back()->with('success', 'Has elogiado al usuario con éxito');
    }
}
