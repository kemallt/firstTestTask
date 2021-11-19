<?php

session_start();

error_reporting( E_ALL );
ini_set('display_errors', 1);

$autoloadPath1 = __DIR__ . '/../../../autoload.php';
$autoloadPath2 = __DIR__ . '/vendor/autoload.php';

if (file_exists($autoloadPath1)) {
    require_once $autoloadPath1;
} else {
    require_once $autoloadPath2;
}

$app = new \App\App();

try {
    echo $app->getHandler();
} catch (\Exception $e) {
    $errorMessage = $e->getMessage();
    echo \App\App::view('notfound', 'Ошибка', ['message' => $errorMessage]);
}