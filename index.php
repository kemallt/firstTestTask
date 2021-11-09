<?php

//$autoloadPath1 = __DIR__ . '/../../../autoload.php';
$autoloadPath2 = __DIR__ . '/vendor/autoload.php';

require_once $autoloadPath2;


////todo вынести механизм миграций в бинарный файл
//$taskMigration = new \App\database\migrations\UsersMigration();
//$taskMigration->up();
//
$taskMigration = new \App\database\migrations\TaskMigration();
$taskMigration->up();

$seeds = new \App\database\seeders\UserSeeder();
$seeds->up();
//
//foreach ($result as $row) {
//    echo "{$row['id']} {$row['name']} {$row['email']} {$row['is_admin']} {$row['password']}" . PHP_EOL;
//}
