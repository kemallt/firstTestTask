<?php

namespace App\Controllers;

use App\App;
use App\Models\User;

class LoginController
{
    public static function getCurrentUser(): ?User
    {
        $userId = $_SESSION['userId'] ?? null;
        return $userId === null ? null : new User($userId);
    }

    public static function discardCurrentUser(): void
    {
        $_SESSION['userId'] = null;
    }

    public function getLoginForm(): string
    {
        return App::view('loginForm', 'Вход');
    }

    public function getRegisterForm(): string
    {
        return App::view('registerForm', 'Регистрация');
    }

    public function login(array $credentials): mixed
    {
        try {
            $user = User::findByName($credentials['name']);
        } catch (\Exception $e) {
            return App::view('loginForm', 'Вход', $credentials);
        }
        if (password_verify($credentials['password'], $user->getPassword())) {
            $_SESSION['userId'] = $user->getId();
            $newUrl = App::getUrl($_SERVER['HTTP_HOST']);
            header("Location: {$newUrl}");
            exit();
        }
        $_SESSION['errors'] = ['Неверный пароль'];
        return App::view('loginForm', 'Вход', $credentials);
    }

    public function logout(): void
    {
        self::discardCurrentUser();        
        $newUrl = App::getUrl($_SERVER['HTTP_HOST']);
        header("Location: {$newUrl}");
    }
    
    public function register(array $userData): string
    {
        $validationResult = $this->validateUserData($userData);
        if ($validationResult['valid']) {
            $user = new User();
            $user->setPassword($userData['password']);
            $user->setEmail($userData['email']);
            $user->setName($userData['name']);
            $user = $user->save();
            $_SESSION['userId'] = $user->getId();
            $_SESSION['messages'] = ['Пользователь успешно зарегистрирован'];
            $newUrl = App::getUrl($_SERVER['HTTP_HOST']);
            header("Location: {$newUrl}");
            exit();
        }
        $_SESSION['errors'] = $validationResult['errors'];
        return App::view('registerForm', 'Вход', ['userData' => $userData]);
    }
    
    private function validateUserData($userData): array
    {
        $valid = true;
        $errors = [];
        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            $valid = false;
            $errors[] = 'Некорректный email';
        }
        if (strlen($userData['name']) < 3) {
            $valid = false;
            $errors[] = 'Имя пользователя должно быть более 3 символов в длину';
        }
        if (strlen($userData['password']) < 3) {
            $valid = false;
            $errors[] = 'Пароль должен быть более 3 символов в длину';
        }
        return ['valid' => $valid, 'errors' => $errors];
    }
}
