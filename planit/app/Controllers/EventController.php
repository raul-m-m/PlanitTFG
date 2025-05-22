<?php

namespace App\Controllers;

use App\Models\EventModel;
use App\Models\UserModel;
use App\Models\EventUserModel;
use App\Models\CategoryModel;
use CodeIgniter\Controller;

/**
 * Clase EventController
 * 
 * Controlador encargado de gestionar las operaciones relacionadas con los eventos,
 * incluyendo la creación, edición, visualización, cancelación y administración de eventos.
 */
class EventController extends Controller
{
    /**
     * @var EventModel Modelo para gestionar los eventos.
     * @var UserModel Modelo para gestionar los usuarios.
     * @var EventUserModel Modelo para gestionar la relación entre eventos y usuarios.
     * @var CategoryModel Modelo para gestionar las categorías de los eventos.
     * @var UserController Controlador para gestionar las operaciones de usuario.
     * @var AdminController Controlador para gestionar las operaciones de administrador.
     */
    protected $eventModel;
    protected $userModel;
    protected $eventUserModel;
    protected $categoryModel;
    protected $userController;
    protected $adminController;

    /**
     * Constructor de la clase.
     * Inicializa los modelos y controladores necesarios.
     */
    public function __construct()
    {
        $this->eventModel = new EventModel();
        $this->userModel = new UserModel();
        $this->eventUserModel = new EventUserModel();
        $this->categoryModel = new CategoryModel();
        $this->userController = new UserController();
        $this->adminController = new AdminController();
    }

    /**
     * Muestra el catálogo de eventos.
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface Vista del catálogo de eventos.
     */
    public function catalog()
    {
        $data['events'] = $this->eventModel->select('events.*, category.nombre as category_name')
            ->join('category', 'events.category_id = category.id', 'left')
            ->orderBy('date', 'asc')
            ->findAll();
        $data['categories'] = $this->categoryModel->findAll();
        $data['user_id'] = session()->get('user_id');
        if ($data['user_id']) {
            $data['attended_events_ids'] = $this->eventUserModel->where('id_user', $data['user_id'])->findColumn('id_event');
        }
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
        $data['isAdmin'] = $this->adminController->isAdmin();
        return view('event/catalog', $data);
    }

    /**
     * Muestra el formulario para crear un nuevo evento.
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface Vista del formulario de creación de eventos.
     */
    public function create()
    {
        $this->userController->checkSession();
        $user = $this->userModel->find(session()->get('user_id'));
        $isAdmin = $this->adminController->isAdmin();
        $data = [
            'user_id' => $user['id'],
            'username' => $user['username'],
            'photo' => $user['photo'] ? base64_encode($user['photo']) : '',
            'isAdmin' => $isAdmin,
        ];
        $data['categories'] = $this->categoryModel->findAll();
        return view('event/create', $data);
    }

    /**
     * Almacena un nuevo evento en la base de datos.
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface Redirección a la lista de eventos o al catálogo de administrador.
     */
    public function store()
    {
        $this->userController->checkSession();
        $categoryName = $this->request->getPost('category');
        $category = $this->categoryModel->where('nombre', $categoryName)->first();
        if (!$category) {
            $this->categoryModel->save(['nombre' => $categoryName]);
            $category = $this->categoryModel->where('nombre', $categoryName)->first();
        }
        $file = $this->request->getFile('image');
        $this->eventModel->save([
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'date' => $this->request->getPost('date'),
            'direccion' => $this->request->getPost('direccion'),
            'category_id' => $category['id'],
            'capacity' => $this->request->getPost('capacity') ?: null,
            'price' => $this->request->getPost('price') ?: null,
            'image' => $file && $file->isValid() ? file_get_contents($file->getTempName()) : null,
            'user_id' => session()->get('user_id'),
            'hour' => $this->request->getPost('hour'),
            'city' => $this->request->getPost('city')
        ]);

        $creatorId = session()->get('user_id');
        $this->userController->modifyPoints($creatorId, 10, 'add');
        $user = $this->userModel->find(session()->get('user_id'));
        if ($user['role_id'] == 2) {
            return redirect()->to('/admin-catalog')->with('success', 'Evento creado con éxito');
        }
        return redirect()->to('/event/events')->with('success', 'Evento creado con éxito');
    }

    /**
     * Muestra los detalles de un evento específico.
     * 
     * @param int $id ID del evento a mostrar.
     * @return \CodeIgniter\HTTP\ResponseInterface Vista con los detalles del evento.
     */
    public function show($id)
    {
        $data['event'] = $this->eventModel->find($id);
        $data['user_id'] = session()->get('user_id');
        if (!$data['event']) {
            return redirect()->to('/catalog');
        }

        $category = $this->categoryModel->find($data['event']['category_id']);
        $data['event']['category'] = $category ? $category['nombre'] : 'Sin categoría';


        $data['isBlocked'] = null;
        if ($data['user_id']) {
            $creatorId = $data['event']['user_id'];
            $blockedCheck = $this->userModel->db->table('blocked_users')->where('id_user', $creatorId)->where('id_blocked', $data['user_id'])->countAllResults();
            $data['isBlocked'] = $blockedCheck > 0 ? true : null;
        }

        $creator = $this->userModel->find($data['event']['user_id']);
        $data['creator_username'] = $creator['username'];
        $data['creator_photo'] = $creator['photo'] ? base64_encode($creator['photo']) : '';
        $data['creator_id'] = $creator['id'];

        $data['isAdmin'] = $this->adminController->isAdmin();

        $isJoined = false;
        if ($data['user_id']) {
            $isJoined = $this->eventUserModel->where('id_user', $data['user_id'])
                ->where('id_event', $id)
                ->first() !== null;
        }

        $this->adminController->isAdmin() ? $data['isAdmin'] = true : null;
        $data['is_joined'] = $isJoined;

        $attendeesCount = $this->eventUserModel->where('id_event', $id)->countAllResults();
        $data['attendees_count'] = $attendeesCount;

        $isFull = $data['event']['capacity'] !== null && $attendeesCount >= $data['event']['capacity'];
        $data['is_full'] = $isFull;
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
        return view('event/show', $data);
    }

    /**
     * Muestra los eventos creados por el usuario en sesion.
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface Vista con los eventos del usuario.
     */
    public function myEvents()
    {
        $this->userController->checkSession();
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);
        if (!$user) {
            session()->remove('user_id');
            return redirect()->to('/login');
        }
        $data['events'] = $this->eventModel->where('user_id', $userId)->orderBy('date', 'asc')->findAll();
        $data['user_id'] = $userId;
        $data['username'] = $user['username'];
        $data['photo'] = $user['photo'] ? base64_encode($user['photo']) : '';
        return view('event/my_events', $data);
    }

    /**
     * Muestra los eventos a los que el usuario en sesion se ha unido.
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface Vista con los eventos unidos por el usuario.
     */
    public function joinedEvents()
    {
        $this->userController->checkSession();

        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);
        if (!$user) {
            session()->remove('user_id');
            return redirect()->to('/login');
        }
        $joinedEvents = $this->eventUserModel->where('id_user', $userId)->findAll();
        $eventIds = array_column($joinedEvents, 'id_event');
        $data['events'] = $eventIds ? $this->eventModel->whereIn('id', $eventIds)->orderBy('date', 'asc')->findAll() : [];
        $data['user_id'] = $userId;
        $data['username'] = $user['username'];
        $data['photo'] = $user['photo'] ? base64_encode($user['photo']) : '';
        return view('event/events', $data);
    }

    /**
     * Muestra el formulario para editar un evento.
     * 
     * @param int $id ID del evento a editar.
     * @return \CodeIgniter\HTTP\ResponseInterface Vista del formulario de edición de eventos.
     */
    public function edit($id)
    {
        $this->userController->checkSession();
        $event = $this->eventModel->find($id);
        if (!$event || $event['user_id'] != session()->get('user_id') && !$this->adminController->isAdmin()) {
            return redirect()->to('/my-events')->with('error', 'No tienes permiso para editar este evento');
        }

        $category = $this->categoryModel->find($event['category_id']);
        $event['category'] = $category ? $category['nombre'] : null;
        $isAdmin = $this->adminController->isAdmin();
        $user = $this->userModel->find(session()->get('user_id'));
        $data = [
            'user_id' => $user['id'],
            'username' => $user['username'],
            'photo' => $user['photo'] ? base64_encode($user['photo']) : '',
            'event' => $event,
            'categories' => $this->categoryModel->findAll(),
            'isAdmin' => $isAdmin,
        ];
        return view('event/edit', $data);
    }

    /**
     * Actualiza un evento existente en la base de datos.
     * 
     * @param int $id ID del evento a actualizar.
     * @return \CodeIgniter\HTTP\ResponseInterface Redirección a la lista de eventos o al catálogo de administrador.
     */
    public function update($id)
    {
        $this->userController->checkSession();
        $event = $this->eventModel->find($id);
        if (!$event || $event['user_id'] != session()->get('user_id') && !$this->adminController->isAdmin()) {
            return redirect()->to('/my-events')->with('error', 'No tienes permiso para editar este evento');
        }
        $categoryName = $this->request->getPost('category');
        $category = $this->categoryModel->where('nombre', $categoryName)->first();
        if (!$category) {
            $this->categoryModel->save(['nombre' => $categoryName]);
            $category = $this->categoryModel->where('nombre', $categoryName)->first();
        }
        $file = $this->request->getFile('image');
        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'date' => $this->request->getPost('date'),
            'hour' => $this->request->getPost('hour'),
            'direccion' => $this->request->getPost('direccion'),
            'category_id' => $category['id'],
            'capacity' => $this->request->getPost('capacity') ?: null,
            'price' => $this->request->getPost('price') ?: null,
            'city' => $this->request->getPost('city'),
        ];
        if ($file && $file->isValid()) {
            $data['image'] = file_get_contents($file->getTempName());
        }
        $this->eventModel->update($id, $data);

        $user = $this->userModel->find(session()->get('user_id'));
        if ($user['role_id'] == 2) {
            return redirect()->to('/admin-catalog')->with('success', 'Evento actualizado con éxito');
        }
        return redirect()->to('/event/events')->with('success', 'Evento actualizado con éxito');
    }

    /**
     * Cancela un evento existente.
     * 
     * @param int $id ID del evento a cancelar.
     * @return \CodeIgniter\HTTP\ResponseInterface Redirección a la lista de eventos o al catálogo de administrador.
     */
    public function cancel($id)
    {
        $this->userController->checkSession();
        $event = $this->eventModel->find($id);
        if (!$event) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Evento no encontrado');
        }
        if ($event['user_id'] !== session()->get('user_id') && !$this->adminController->isAdmin()) {
            return redirect()->back()->with('error', 'No tienes permiso para cancelar este evento');
        }
        $this->eventModel->update($id, ['canceled' => 'si']);
        $user = $this->userModel->find(session()->get('user_id'));

        if ($user['role_id'] == 2) {
            return redirect()->to('/admin-catalog')->with('success', 'Evento cancelado con éxito');
        }
        return redirect()->to('/event/events')->with('success', 'Evento cancelado con éxito');
    }
}
