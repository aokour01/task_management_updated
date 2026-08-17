<?php

namespace App\Controllers;

use App\Core\Validator;
use App\Repositories\UserRepository;

class AuthController extends Controller
{
    public function __construct(private UserRepository $users)
    {
    }

    public function showRegisterForm(): void
    {
        $this->render('auth/register', ['errors' => [], 'name' => '', 'email' => '']);
    }

    public function register(): void
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        $validator = new Validator($_POST);
        $validator->required('name', 'Name')
            ->required('email', 'Email')
            ->email('email', 'Email')
            ->minLength('password', 6, 'Password')
            ->matches('password', 'password_confirm', 'Password confirmation');

        $errors = $validator->errors();

        if (empty($errors) && $this->users->emailExists($email)) {
            $errors[] = 'That email is already registered.';
        }

        if (!empty($errors)) {
            $this->render('auth/register', compact('errors', 'name', 'email'));
            return;
        }

        $userId = $this->users->create($name, $email, $_POST['password']);
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;

        $this->redirect('/tasks');
    }

    public function showLoginForm(): void
    {
        $this->render('auth/login', ['errors' => [], 'email' => '']);
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $validator = new Validator($_POST);
        $validator->required('email', 'Email')->required('password', 'Password');
        $errors = $validator->errors();

        $user = null;

        if (empty($errors)) {
            $user = $this->users->findByEmail($email);

            if (!$user || !$this->users->verifyPassword($password, $user['password'])) {
                $errors[] = 'Invalid email or password.';
            }
        }

        if (!empty($errors)) {
            $this->render('auth/login', compact('errors', 'email'));
            return;
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $this->redirect('/tasks');
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        $this->redirect('/login');
    }
}
