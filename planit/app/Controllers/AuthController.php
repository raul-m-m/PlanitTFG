<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

/**
 * Clase AuthController
 * 
 * Controlador para gestionar la autenticación de usuarios, incluyendo el inicio de sesión, 
 * registro, y cierre de sesión
 */
class AuthController extends Controller
{
    /**
     * Muestra la vista de inicio de sesión.
     * 
     * @return \CodeIgniter\View\View Vista del formulario de inicio de sesión.
     */
    public function login()
    {
        return view('auth/login');
    }

    /**
     * Procesa el inicio de sesión del usuario.
     * 
     * Verifica las credenciales proporcionadas y, si son válidas, inicia sesión al usuario.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirige al catálogo o al catálogo de administrador según el rol.
     */
    public function doLogin()
    {
        $model = new UserModel();
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $user = $model->where('username', $username)->first();
        if ($user && password_verify($password, $user['password'])) {
            session()->set('user_id', $user['id']);
            setcookie('login_username', '', time() - 3600, '/');
            if ($user['role_id'] == 2) {
                return redirect()->to('/admin-catalog');
            } else {
                return redirect()->to('/catalog');
            }
        } else {
            setcookie('login_username', $username, time() + 300, '/');
            return redirect()->to('/login')->with('error', 'Usuario o contraseña incorrectos');
        }
    }

    /**
     * Muestra la vista de registro de usuario.
     * 
     * @return \CodeIgniter\View\View Vista del formulario de registro.
     */
    public function register()
    {
        return view('auth/register');
    }

    /**
     * Procesa el registro de un nuevo usuario.
     * 
     * Valida los datos proporcionados, verifica que el nombre de usuario no exista, 
     * y guarda al nuevo usuario en la base de datos.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirige al catálogo o al formulario de registro con un mensaje de error.
     */
    public function doRegister()
    {
        $model = new UserModel();

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $passwordConfirm = $this->request->getPost('password_confirm');
        $location = $this->request->getPost('location');
        $file = $this->request->getFile('photo');

        if ($password !== $passwordConfirm) {

            setcookie('register_username', $username, time() + 300, '/');
            setcookie('register_location', $location, time() + 300, '/');
            return redirect()->to('/register')->with('error', 'Las contraseñas no coinciden');
        }
        $usernameExists = $model->where('username', $username)->first();
        if ($usernameExists) {
            setcookie('register_username', $username, time() + 300, '/');
            setcookie('register_location', $location, time() + 300, '/');
            return redirect()->to('/register')->with('error', 'Ya existe un usuario con este nombre');
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $data = [
            'username' => $username,
            'password' => $hashedPassword,
            'location' => $location
        ];

        if ($file && $file->isValid()) {
            $data['photo'] = file_get_contents($file->getTempName());
        } else {
            $data['photo'] = file_get_contents(FCPATH . 'imgs/planit-logo.png');
        }

        $model->save($data);
        $userId = $model->insertID();

        setcookie('register_username', '', time() - 3600, '/');
        setcookie('register_location', '', time() - 3600, '/');

        session()->set('user_id', $userId);

        return redirect()->to('/catalog');
    }

    /**
     * Cierra la sesión del usuario actual.
     * 
     * Elimina la sesión activa del usuario y redirige al catálogo.
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse Redirige al catálogo.
     */
    public function logout()
    {
        session()->remove('user_id');
        return redirect()->to('/catalog');
    }
}
