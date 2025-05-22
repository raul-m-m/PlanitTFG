<?php

namespace App\Controllers;

use App\Models\EventUserModel;
use App\Models\EventModel;
use App\Models\CommentModel;
use App\Models\UserModel;
use App\Controllers\AdminController;
use App\Controllers\UserController;
use CodeIgniter\Controller;

/**
 * Clase EventUserController
 * 
 * Controlador para gestionar la relación entre usuarios y eventos. 
 * Permite a los usuarios unirse o salir de eventos, procesar pagos, 
 * gestionar comentarios y realizar otras acciones relacionadas con eventos.
 */
class EventUserController extends Controller
{
    protected $eventUserModel;
    protected $eventModel;
    protected $userController;
    protected $commentModel;
    protected $userModel;
    protected $adminController;

    public function __construct()
    {
        $this->eventUserModel = new EventUserModel();
        $this->eventModel = new EventModel();
        $this->userController = new UserController();
        $this->commentModel = new CommentModel();
        $this->userModel = new UserModel();
        $this->adminController = new AdminController();
    }

    /**
     * Permite a un usuario en sesion unirse a un evento.
     * 
     * Valida la disponibilidad del evento y la capacidad antes de registrar al usuario.
     * 
     * @param int $eventId ID del evento al que el usuario desea unirse.
     * @return \CodeIgniter\HTTP\RedirectResponse Redirige con un mensaje de éxito o error.
     */
    public function join($eventId)
    {
        $this->userController->checkSession();
        $userId = session()->get('user_id');
        $event = $this->eventModel->find($eventId);

        if (!$event) {
            return redirect()->back()->with('error', 'Evento no encontrado.');
        }

        if ($event['date'] < date('Y-m-d')) {
            return redirect()->back()->with('error', 'No puedes apuntarte a un evento pasado.');
        }

        $existing = $this->eventUserModel->where('id_user', $userId)
            ->where('id_event', $eventId)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Ya estás apuntado a este evento.');
        }

        $attendees_count = $this->eventUserModel->where('id_event', $eventId)->countAllResults();
        if ($event['capacity'] && $attendees_count >= $event['capacity']) {
            return redirect()->back()->with('error', 'El evento está lleno.');
        }

        $data = ['id_user' => $userId, 'id_event' => $eventId];
        $this->eventUserModel->insert($data);

        $creatorId = $event['user_id'];
        $attendeeId = session()->get('user_id');
        $this->userController->modifyPoints($creatorId, 10, 'add');
        $this->userController->modifyPoints($attendeeId, 5, 'add');

        return redirect()->to('my-events')->with('success', 'Te has apuntado al evento con éxito.');
    }

    /**
     * Permite a un usuario salir de un evento.
     * 
     * Valida que el usuario esté registrado en el evento antes de eliminarlo.
     * 
     * @param int $eventId ID del evento del que el usuario desea salir.
     * @return \CodeIgniter\HTTP\RedirectResponse Redirige con un mensaje de éxito o error.
     */
    public function leave($eventId)
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión para desapuntarte.');
        }

        $userId = session()->get('user_id');
        $event = $this->eventModel->find($eventId);

        if (!$event) {
            return redirect()->back()->with('error', 'Evento no encontrado.');
        }

        $existing = $this->eventUserModel->where('id_user', $userId)
            ->where('id_event', $eventId)
            ->first();

        if (!$existing) {
            return redirect()->back()->with('error', 'No estás apuntado a este evento.');
        }

        $creatorId = $event['user_id'];
        $attendeeId = session()->get('user_id');
        $this->userController->modifyPoints($creatorId, 10, 'subtract');
        $this->userController->modifyPoints($attendeeId, 5, 'subtract');

        $this->eventUserModel->where('id_user', $userId)
            ->where('id_event', $eventId)
            ->delete();

        return redirect()->back()->with('success', 'Te has desapuntado del evento con éxito.');
    }

    /**
     * Procesa el pago para un evento.
     * 
     * Valida los datos de la tarjeta de crédito y simula el proceso de pago.
     * 
     * @param int $event_id ID del evento para el cual se realiza el pago.
     * @return \CodeIgniter\HTTP\RedirectResponse Redirige con un mensaje de éxito o error.
     */
    public function processPayment($event_id)
    {
        $this->userController->checkSession();

        $event = $this->eventModel->find($event_id);
        if (!$event) {
            return redirect()->to('/catalog')->with('error', 'Evento no encontrado');
        }

        if (!$event['price'] || $event['price'] <= 0) {
            return redirect()->to('/events/' . $event_id)->with('error', 'Este evento no requiere pago');
        }

        $cardNumber = $this->request->getPost('card_number');
        $cardHolder = $this->request->getPost('card_holder');
        $expiryDate = $this->request->getPost('expiry_date');
        $cvv = $this->request->getPost('cvv');

        $errors = [];

        if (!preg_match('/^\d{16}$/', str_replace(' ', '', $cardNumber))) {
            $errors[] = 'El número de tarjeta no es valido.';
        }

        if (empty($cardHolder)) {
            $errors[] = 'El nombre del titular es obligatorio.';
        }

        if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expiryDate)) {
            $errors[] = 'La fecha de caducidad debe tener el formato MM/AA.';
        } else {
            $expiryParts = explode('/', $expiryDate);
            $month = (int)$expiryParts[0];
            $year = (int)$expiryParts[1] + 2000;
            $currentYear = (int)date('Y');
            $currentMonth = (int)date('m');
            if ($year < $currentYear || ($year == $currentYear && $month < $currentMonth)) {
                $errors[] = 'La tarjeta no es válida. (Caducada)';
            }
        }

        if (!preg_match('/^\d{3}$/', $cvv)) {
            $errors[] = 'El CVV debe tener 3 dígitos numéricos.';
        }

        if (!empty($errors)) {
            return redirect()->back()->with('errors', $errors);
        }

        $paymentSuccess = true;

        if ($paymentSuccess) {
            $this->eventUserModel->insert([
                'id_user' => session()->get('user_id'),
                'id_event' => $event_id
            ]);

            return redirect()->to('/my-events')->with('success', 'Pago simulado con éxito. Te has apuntado al evento.');
        } else {
            session()->setFlashdata('error', 'Error al procesar el pago. Intenta de nuevo.');
            return redirect()->to('/events/' . $event_id);
        }
    }

    /**
     * Muestra los comentarios de un evento.
     * 
     * Recupera los comentarios asociados al evento y verifica los permisos del usuario.
     * 
     * @param int $eventId ID del evento cuyos comentarios se desean mostrar.
     * @return \CodeIgniter\View\View Vista con los comentarios del evento.
     */
    public function showComments($eventId)
    {
        $event = $this->eventModel->find($eventId);
        if (!$event) {
            return redirect()->to('/catalog')->with('error', 'Evento no encontrado');
        }

        $user = $this->userModel->find(session()->get('user_id'));
        if (!$user) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión para ver los comentarios.');
        }
        $comments = $this->commentModel->where('id_event', $eventId)->findAll();

        $commentsWithAuthors = [];
        foreach ($comments as $comment) {
            $commentUser = $this->userModel->find($comment['id_user']);
            $commentsWithAuthors[] = [
                'comment' => $comment['comment'],
                'username' => $commentUser ? $commentUser['username'] : 'Usuario desconocido',
                'created_at' => $comment['created_at'],
                'id_user' => $comment['id_user'],
                'id' => $comment['id'],
            ];
        }
        $hasAttended = $this->eventUserModel->where('id_user', $user['id'])
            ->where('id_event', $eventId)
            ->first() !== null;

        $isCreator = $event['user_id'] == $user['id'];

        $eventHasPassed = $event['date'] < date('Y-m-d');

        $canComment = ($hasAttended || $isCreator) && $eventHasPassed;

        $data = [
            'comments' => $commentsWithAuthors,
            'title' => $event['title'],
            'event_creator' => $event['user_id'],
            'image' => $event['image'] ? base64_encode($event['image']) : null,
            'event_id' => $eventId,         
            'username' => $user['username'],
            'photo' => $user['photo'] ? base64_encode($user['photo']) : null,
            'user_id' => $user['id'],      
            'can_comment' => $canComment,  
            'event_date' => $event['date'],
            'session_id' => session()->get('user_id'),
            'isAdmin' => $this->adminController->isAdmin()
        ];

        return view('event/comments', $data);
    }

    /**
     * Permite a un usuario añadir un comentario a un evento.
     * 
     * Valida que el usuario haya asistido al evento o sea el creador, 
     * y que el evento ya haya pasado.
     * 
     * @param int $eventId ID del evento al que se desea añadir el comentario.
     * @return \CodeIgniter\HTTP\RedirectResponse Redirige con un mensaje de éxito o error.
     */
    public function addComment($eventId)
    {
        $this->userController->checkSession();
        $userId = session()->get('user_id');

        $event = $this->eventModel->find($eventId);
        if (!$event) {
            return redirect()->to('/catalog')->with('error', 'Evento no encontrado');
        }

        $hasAttended = $this->eventUserModel->where('id_user', $userId)
            ->where('id_event', $eventId)
            ->first() !== null;

        $isCreator = $event['user_id'] == $userId;

        if (!$hasAttended && !$isCreator) {
            return redirect()->to("/event/comments/$eventId")->with('error', 'No puedes comentar porque no asististe a este evento ni eres el creador.');
        }

        if ($event['date'] >= date('Y-m-d')) {
            return redirect()->to("/event/comments/$eventId")->with('error', 'No puedes comentar hasta que el evento haya pasado.');
        }

        $commentText = $this->request->getPost('comment');
        if (empty($commentText)) {
            return redirect()->to("/event/comments/$eventId")->with('error', 'El comentario no puede estar vacío.');
        }

        $data = [
            'id_event' => $eventId,
            'id_user' => $userId,
            'comment' => $commentText,
        ];
        $this->commentModel->insert($data);

        return redirect()->to("/events/showComments/$eventId")->with('success', 'Comentario añadido con éxito.');
    }

    /**
     * Permite eliminar un comentario de un evento.
     * 
     * Valida que el usuario sea el autor del comentario, el creador del evento, 
     * o un administrador antes de eliminarlo.
     * 
     * @param int $commentId ID del comentario a eliminar.
     * @return \CodeIgniter\HTTP\RedirectResponse Redirige con un mensaje de éxito o error.
     */
    public function deleteComment($commentId)
    {
        $this->userController->checkSession();
        $userId = session()->get('user_id');

        $comment = $this->commentModel->find($commentId);
        if (!$comment) {
            return redirect()->to('/catalog')->with('error', 'Comentario no encontrado');
        }

        $event = $this->eventModel->find($comment['id_event']);
        if (!$event) {
            return redirect()->to('/catalog')->with('error', 'Evento no encontrado');
        }

        if ($comment['id_user'] != $userId && $event['user_id'] != $userId && !$this->adminController->isAdmin()) {
            return redirect()->to("/events/comments/{$event['id']}")->with('error', 'No tienes permisos para eliminar este comentario.');
        }

        $this->commentModel->where('id', $commentId)->delete();
        return redirect()->to("/events/showComments/{$event['id']}")->with('success', 'Comentario eliminado con éxito.');
    }
}
