<?php

namespace App\Controllers;

use App\Models\EventModel;
use App\Models\UserModel;
use App\Models\EventUserModel;
use App\Models\CategoryModel;
use App\Controllers\UserController;
use CodeIgniter\Controller;

/**
 * Clase AdminController
 * 
 * Controlador para gestionar las funcionalidades de administrador de la aplicación, 
 * como la gestión de eventos, usuarios, categorías y puntos. 
 * Requiere que el usuario tenga permisos de administrador (id_rol = 2).
 */
class AdminController extends Controller
{
    private $eventUserModel;
    private $userModel;
    private $eventModel;
    private $categoryModel;
    private $userController;
    public function __construct()
    {
        $this->eventUserModel = new EventUserModel();
        $this->userModel = new UserModel();
        $this->eventModel = new EventModel();
        $this->categoryModel = new CategoryModel();
        $this->userController = new UserController();
    }

    /**
     * Verifica si el usuario actual tiene permisos de administrador.
     * 
     * @return bool Devuelve true si el usuario es administrador, de lo contrario false.
     */
    public function isAdmin()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return false;
        }

        $user = $this->userModel->find($userId);

        if ($user['role_id'] == 2) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Elimina a un usuario de un evento específico.
     * 
     * @param int $userId ID del usuario a eliminar.
     * @param int $eventId ID del evento del que se eliminará al usuario.
     * @return \CodeIgniter\HTTP\RedirectResponse Redirige con un mensaje de éxito o error.
     */
    public function removeFromEvent($userId, $eventId)
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/catalog')->with('error', 'No tienes permisos para realizar esta acción');
        }
        $event = $this->eventModel->find($eventId);
        $creatorId = $event['user_id'];
        $attendeeId = $userId;
        $this->userController->modifyPoints($creatorId, 10, 'subtract');
        $this->userController->modifyPoints($attendeeId, 5, 'subtract');
        $this->eventUserModel->where('id_user', $userId)
            ->where('id_event', $eventId)
            ->delete();

        return redirect()->to('/admin/userAttendedEvents/' . $userId)->with('success', 'Usuario eliminado del evento con éxito');
    }

    /**
     * Muestra los eventos a los que un usuario específico ha asistido.
     * 
     * @param int $userId ID del usuario.
     * @return \CodeIgniter\HTTP\RedirectResponse|\CodeIgniter\View\View Redirige o muestra la vista con los eventos.
     */
    public function userAttendedEvents($userId)
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/catalog')->with('error', 'No tienes permisos para realizar esta acción');
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'Usuario no encontrado');
        }

        $joinedEvents = $this->eventUserModel->where('id_user', $userId)->findAll();
        $eventIds = array_column($joinedEvents, 'id_event');
        $events = $eventIds ? $this->eventModel->select('events.*, category.nombre as category_name')
            ->join('category', 'events.category_id = category.id', 'left')
            ->whereIn('events.id', $eventIds)
            ->findAll() : [];

        $data = [
            'events' => $events,
            'username' => $user['username'],
            'userId' => $userId,
        ];

        return view('admin/userAttendedEvents', $data);
    }

    /**
     * Rehabilita un evento previamente cancelado.
     * 
     * @param int $id ID del evento a rehabilitar.
     * @return \CodeIgniter\HTTP\RedirectResponse Redirige con un mensaje de éxito o error.
     */
    public function rehabilitate($id)
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/catalog')->with('error', 'No tienes permisos para realizar esta acción');
        }
        $event = $this->eventModel->find($id);
        if (!$event) {
            return redirect()->to('/admin-catalog')->with('error', 'Evento no encontrado');
        }
        $this->eventModel->update($id, ['canceled' => 'no']);
        return redirect()->to('/admin-catalog')->with('success', 'Evento rehabilitado con éxito');
    }

    /**
     * Elimina un evento específico.
     * 
     * @param int $id ID del evento a eliminar.
     * @return \CodeIgniter\HTTP\RedirectResponse Redirige con un mensaje de éxito o error.
     */
    public function delete($id)
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/catalog')->with('error', 'No tienes permisos para realizar esta acción');
        }
        $event = $this->eventModel->find($id);
        if (!$event) {
            return redirect()->to('/admin-catalog')->with('error', 'Evento no encontrado');
        }
        $this->eventModel->delete($id);

        $creatorId = $event['user_id'];
        $this->userController->modifyPoints($creatorId, 10, 'subtract');
        return redirect()->to('/admin-catalog')->with('success', 'Evento eliminado con éxito');
    }

    /**
     * Muestra el catálogo de eventos en vista de administrador.
     * 
     * @return \CodeIgniter\View\View Vista del catálogo de eventos.
     */
    public function adminCatalog()
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/catalog');
        }

        $evento = $this->request->getGet('evento');

        $query = $this->eventModel->select('events.*, category.nombre as category_name')
            ->join('category', 'events.category_id = category.id', 'left');

        if ($evento) {
            $query->like('title', $evento, 'both');
        }

        $data['events'] = $query->findAll();

        $data['categories'] = $this->categoryModel->findAll();
        $data['user_id'] = session()->get('user_id');
        $data['isAdmin'] = true;

        if ($data['user_id']) {
            $user = $this->userModel->find($data['user_id']);
            if ($user) {
                $data['username'] = $user['username'];
                $data['photo'] = $user['photo'] ? base64_encode($user['photo']) : '';
            } else {
                session()->remove('user_id');
                $data['user_id'] = null;
            }
        }

        return view('admin/admin_catalog', $data);
    }

    /**
     * Muestra los eventos creados por un usuario específico.
     * 
     * @param int $id ID del usuario.
     * @return \CodeIgniter\View\View Vista con los eventos del usuario.
     */
    public function viewUserEvents($id)
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/catalog')->with('error', 'No tienes permisos para realizar esta acción');
        }
        $user = $this->userModel->find($id);
        $events = $this->eventModel->where('user_id', $id)->findAll();
        return view('admin/viewUserEvents', [
            'username' => $user['username'],
            'events' => $events
        ]);
    }

    /**
     * Muestra la lista de usuarios registrados.
     * 
     * @return \CodeIgniter\View\View Vista con la lista de usuarios.
     */
    public function viewUsers()
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/login');
        }


        $username = $this->request->getGet('username');
        if ($username) {
            $data['users'] = $this->userModel->where('role_id', 1)->like('username', $username, 'both')->findAll();
        } else {
            $data['users'] = $this->userModel->where('role_id', 1)->findAll();
        }

        return view('admin/viewUsers', $data);
    }

    /**
     * Muestra la lista de categorías existentes.
     * 
     * @return \CodeIgniter\View\View Vista con la lista de categorías.
     */
    public function viewCategories()
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/login');
        }
        $category = $this->request->getGet('category');
        if (isset($category)) {
            $data['categories'] = $this->categoryModel->like('nombre', $category, 'both')->findAll();
        } else {
            $data['categories'] = $this->categoryModel->findAll();
        }
        return view('admin/categories', $data);
    }

    /**
     * Elimina una categoría específica y los eventos asociados a ella.
     * 
     * @param int $id ID de la categoría a eliminar.
     * @return \CodeIgniter\HTTP\RedirectResponse Redirige con un mensaje de éxito.
     */
    public function deleteCategory($id)
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/login');
        }
        $this->eventModel->where('category_id', $id)->delete();
        $this->categoryModel->delete($id);
        return redirect()->to('/admin/categories')->with('success', 'Categoría eliminada con éxito');
    }

    /**
     * Muestra el formulario para crear una nueva categoría.
     * 
     * @return \CodeIgniter\View\View Vista del formulario de creación de categoría.
     */
    public function createCategory()
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/login');
        }
        return view('admin/createCategory');
    }

    /**
     * Almacena una nueva categoría en la base de datos.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirige con un mensaje de éxito.
     */
    public function storeCategory()
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/login');
        }
        $nombre = $this->request->getPost('name');
        $this->categoryModel->insert(['nombre' => $nombre]);
        return redirect()->to('/admin/categories')->with('success', 'Categoría creada con éxito');
    }

    /**
     * Muestra los puntos de un usuario específico.
     * 
     * @param int $id ID del usuario.
     * @return \CodeIgniter\View\View Vista con los puntos del usuario.
     */
    public function viewUserPoints($id)
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/login');
        }
        $user = $this->userModel->find($id);
        return view('admin/userPoints', [
            'username' => $user['username'],
            'points' => $user['points'],
            'photo' => $user['photo'] ? base64_encode($user['photo']) : '',
            'id' => $user['id']
        ]);
    }

    /**
     * Actualiza los puntos de un usuario específico.
     * 
     * @param int $id ID del usuario.
     * @return \CodeIgniter\HTTP\RedirectResponse Redirige con un mensaje de éxito.
     */
    public function updatePoints($id)
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/login');
        }
        $points = $this->request->getPost('points');
        $this->userModel->update($id, ['points' => $points]);
        return redirect()->to('/admin/viewUserPoints/' . $id . '')->with('success', 'Puntos actualizados con éxito');
    }
}
