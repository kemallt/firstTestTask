<?php

namespace App;

use App\Controllers\LoginController;
use App\Controllers\TaskController;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class App
{
    private string $route = "";
    private string $queryParameter = "";
    private ?array $data = null;
    private static bool $isAdmin = false;
    
    public function __construct()
    {
        $this->setRoute();
    }

    public static function getUrl($host)
    {
        $protocol = array_key_exists('HTTPS', $_SERVER) ? "https" : "http";
        return $protocol . '://' . $host;         
    }
    
    public static function view(string $viewName, string $title, array $params = []): string
    {
        if (empty($_SESSION['token'])) {
            $_SESSION['token'] = bin2hex(random_bytes(32));
        }
        $token = $_SESSION['token'];
        $host = self::getUrl($_SERVER['HTTP_HOST']);
        $registerAddress = "{$host}/register";
        $createAddress = "{$host}/create";

        try {
            $currentUser = LoginController::getCurrentUser();
        } catch (\Exception $e) {
            $currentUser = null;
            $_SESSION['errors'] = ["Не удалось определить пользователя - " . $e->getMessage()];
            LoginController::discardCurrentUser();
        }
        if ($currentUser === null) {
            $isAdmin = false;
            $loginText = 'Вход';
            $loginAddress = "{$host}/login";
            $showRegisterAddress = true;
        } else {
            $isAdmin = (bool)$currentUser->getIsAdmin();
            $loginText = 'Выход';
            $loginAddress = "{$host}/logout";
            $showRegisterAddress = false;
        }
        if (array_key_exists('messages', $_SESSION)) {
            $messages = $_SESSION['messages'];
            $_SESSION['messages'] = [];
        } else {
            $messages = [];
        }
        if (array_key_exists('errors', $_SESSION)) {
            $errors = $_SESSION['errors'];
            $_SESSION['errors'] = [];
        } else {
            $errors = [];
        }
        
        $loader = new FilesystemLoader(__DIR__ . '/Views');
        $twig = new Environment($loader);
        return $twig->render("{$viewName}.html.twig", array_merge([
            'token' => $token,
            'title' => $title, 
            'host' => $host,
            'isAdmin' => $isAdmin,
            'loginAddress' => $loginAddress,
            'loginText' => $loginText,
            'registerAddress' => $registerAddress,
            'showRegisterAddress' => $showRegisterAddress,
            'createAddress' => $createAddress,
            'messages' => $messages,
            'errors' => $errors
        ], $params));
    }
    
    public function getHandler(): string
    {
        switch ($this->route) {
            case "":
                $controller = new TaskController();
                return $controller->showTasks();
            case "create":
                $controller = new TaskController();
                return $controller->createTask();
            case "edit":
                $controller = new TaskController();
                $taskId = (int)$this->queryParameter;
                return $controller->editTask($taskId);
            case "store":
                $controller = new TaskController();
                return $controller->storeTask($this->data);
            case "update":
                $controller = new TaskController();
                return $controller->updateTask($this->data);
            case "loginform":
                $controller = new LoginController();
                return $controller->getLoginForm();
            case "login":
                $controller = new LoginController();
                return $controller->login($this->data);
            case "logout":
                $controller = new LoginController();
                $controller->logout();
                break;
            case "registerForm":
                $controller = new LoginController();
                return $controller->getRegisterForm();
            case "register":
                $controller = new LoginController();
                return $controller->register($this->data);
            case "notfound":
                return self::view('notfound', 'Page not found', ['message' => 'page not found']);
        }
        return self::view('notfound', 'Page not found', ['message' => 'page not found']);
    }
    
    public function setRoute()
    {
        $requestUrl = explode('?', $_SERVER['REQUEST_URI']);
        if (count($requestUrl) > 0) {
            $urlData = $requestUrl[0];
        } else {
            $urlData = $requestUrl;
        }
        $url = explode('/', $urlData);
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method === 'GET') {
            if ($url[1] === "") {
                $this->route = "";
                return;
            }
            if ($url[1] === "create") {
                $this->route = "create";
                return;
            }
            if ($url[1] === "edit" && count($url) === 3) {
                $this->route = "edit";
                $this->queryParameter = $url[2];
                return;
            }
            if ($url[1] === "login") {
                $this->route = "loginform";
                return;
            }
            if ($url[1] === "logout") {
                $this->route = "logout";
                return;
            }
            if ($url[1] === "register") {
                $this->route = "registerForm";
                return;
            }
        }
        if ($method === 'POST') {
            if ($url[1] === 'store' && array_key_exists('task', $_POST)) {
                $this->route = "store";
                $this->data = $_POST['task'];
                return;
            }
            if ($url[1] === "edit" && array_key_exists('task', $_POST)) {
                $this->route = "update";
                $this->data = $_POST['task'];
                return;
            }
            if ($url[1] === "login" && array_key_exists('credentials', $_POST)) {
                $this->route = "login";
                $this->data = $_POST['credentials'];
                return;
            }
            if ($url[1] === "register" && array_key_exists('userData', $_POST)) {
                $this->route = "register";
                $this->data = $_POST['userData'];
                return;
            }
        }
        $this->route = "notfound";
    }
}
