<?php

namespace App\Controllers;

use App\App;
use App\Models\User;

class LoginController
{
    public function showLoginForm()
    {
        return App::view('loginForm', 'Вход');
    }

    public function login(array $credentials): mixed
    {
        try {
            $user = User::findByName($credentials['name']);
        } catch (\Exception $e) {
            return App::view('loginForm', 'Вход', $credentials);
        }
        if ($user->getPassword() === password_hash($credentials['password'], PASSWORD_DEFAULT)) {
            $_SESSION['userId'] = $user->getId();
            $newUrl = App::getUrl($_SERVER['HTTP_HOST']);
            header("Location: {$newUrl}");
            exit();
        }
        return "";
    }

    public function logout(): void
    {
        $_SESSION['userId'] = null;
        $newUrl = App::getUrl($_SERVER['HTTP_HOST']);
        header("Location: {$newUrl}");
    }
    
    public static function getCurrentUser(): ?User
    {
        $userId = $_SESSION['userId'] ?? null;
        return $userId === null ? null : new User($userId);
    }

    public function showRegisterForm(): string
    {
        return App::view('registerForm', 'Регистрация');
    }
    
    public function register(array $userData): string
    {
        $validationResult = $this->validateUserData($userData);
        if ($validationResult['valid']) {
            $user = new User();
            $user->setPassword(password_hash($userData['password'], PASSWORD_DEFAULT));
            $user->setEmail($userData['email']);
            $user->setName($userData['name']);
            $userId = $user->save();
            $_SESSION['userId'] = $userId;
            $newUrl = App::getUrl($_SERVER['HTTP_HOST']);
            header("Location: {$newUrl}");
            exit();
        }
        return App::view('registerForm', 'Вход', ['userData' => $userData, 'errors' => $validationResult['errors']]);
    }
    
    private function validateUserData($userData): array
    {
        $valid = true;
        $errors = [];
        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            $valid = false;
            $errors['email'] = 'invalid email';
        }
        if (strlen($userData['name']) < 3) {
            $valid = false;
            $errors['name'] = 'must be more then 3 chars';
        }
        if (strlen($userData['password'])) {
            $valid = false;
            $errors['password'] = 'must be more then 3 chars';
        }
        return ['valid' => $valid, 'errors' => $errors];
    }
}
