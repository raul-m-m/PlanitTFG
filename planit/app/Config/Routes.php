<?php
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'EventController::catalog');
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::doLogin');
$routes->get('/register', 'AuthController::register');
$routes->post('/register', 'AuthController::doRegister');
$routes->get('/catalog', 'EventController::catalog');
$routes->get('/events/create', 'EventController::create');
$routes->post('/events/store', 'EventController::store');
$routes->get('/logout', 'AuthController::logout');
$routes->get('/events/(:num)', 'EventController::show/$1');
$routes->get('/profile/(:num)', 'UserController::profile/$1');
$routes->get('/profile/', 'UserController::profile');
$routes->post('/profile/update/(:num)', 'UserController::update/$1');
$routes->get('/my-events', 'EventController::joinedEvents');
$routes->get('/events/edit/(:num)', 'EventController::edit/$1');
$routes->post('/events/update/(:num)', 'EventController::update/$1');
$routes->get('/events/cancel/(:num)', 'EventController::cancel/$1');
$routes->get('/event/join/(:num)', 'EventUserController::join/$1');
$routes->get('/event/leave/(:num)', 'EventUserController::leave/$1');
$routes->get('/event/events', 'EventController::myevents');
$routes->get('/admin-catalog', 'AdminController::adminCatalog');
$routes->get('/events/delete/(:num)', 'AdminController::delete/$1');
$routes->get('/events/rehabilitate/(:num)', 'AdminController::rehabilitate/$1');
$routes->get('/admin/users', 'AdminController::viewUsers');
$routes->get('/admin/viewUserEvents/(:num)', 'AdminController::viewUserEvents/$1');
$routes->get('/users/delete/(:num)', 'UserController::delete/$1');
$routes->get('/users/delete/', 'UserController::delete');
$routes->post('/map/mover-mapa', 'MapController::moverMapa');
$routes->post('/map/cambiar-direccion', 'MapController::cambiarDireccion');
$routes->get('/admin/userAttendedEvents/(:num)', 'AdminController::userAttendedEvents/$1');
$routes->get('/admin/removeFromEvent/(:num)/(:num)', 'AdminController::removeFromEvent/$1/$2');
$routes->get('/users/blockedUsers', 'UserController::blockedUsers');
$routes->get('/user/block/(:num)', 'UserController::block/$1');
$routes->get('/user/unblock/(:num)', 'UserController::unblock/$1');
$routes->get('/admin/categories', 'AdminController::viewCategories');
$routes->get('/admin/createCategory', 'AdminController::createCategory/');
$routes->post('/admin/storeCategory', 'AdminController::storeCategory');
$routes->get('/admin/deleteCategory/(:num)', 'AdminController::deleteCategory/$1');
$routes->get('/users/attendees/(:num)', 'UserController::showAttendees/$1');
$routes->get('/users/blockUser/(:num)', 'UserController::blockUser/$1');
$routes->post('/event/process-payment/(:num)', 'EventUserController::processPayment/$1');
$routes->get('/user/praise/(:num)', 'UserController::praise/$1');
$routes->get('/user/(:any)', 'UserController::show/$1');
$routes->get('/admin/viewUserPoints/(:num)', 'AdminController::viewUserPoints/$1');
$routes->post('/admin/updatePoints/(:num)', 'AdminController::updatePoints/$1');
$routes->get('/events/showComments/(:num)', 'EventUserController::showComments/$1');
$routes->post('/events/addComment/(:num)', 'EventUserController::addComment/$1');
$routes->get('/events/deleteComment/(:num)', 'EventUserController::deleteComment/$1');