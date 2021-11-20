<?php

namespace Tests;

use App\Controllers\AuthController;
use App\database\migrations\TasksMigration;
use App\database\migrations\UsersMigration;
use App\database\seeders\UsersSeeder;
use App\DatabaseConnect;
use PHPUnit\Framework\TestCase;

if (!isset($_SESSION)) {
    if (PHP_SAPI === 'cli') {
        $_SESSION = array();
    }
}
class AuthControllerTest extends TestCase
{
    public static $shared_session = array();
    
    public function setUp(): void
    {
        $_SESSION = AuthControllerTest::$shared_session;
        
        $config = [
            'db_connection' => 'mysql',
            'db' => 'test',
            'user' => 'root',
            'password' => 'root',
            'host' => 'db2',
            'port' => 3306
        ];

        $this->connect = DatabaseConnect::getConnect($config);
        $migrationUsers = new UsersMigration();
        $migrationTasks = new TasksMigration();
        $migrationTasks->down();
        $migrationUsers->down();
        $migrationUsers->up();
        $migrationTasks->up();
        $seederUsers = new UsersSeeder();
        $seederUsers->up();
    }

    public function testGetCurrentUser()
    {
        $this->assertNull(AuthController::getCurrentUser());
        $_SESSION['userId'] = 1;
        $currentUser = AuthController::getCurrentUser();
        $this->assertEquals($_SESSION['userId'], $currentUser->getId());
    }
}
