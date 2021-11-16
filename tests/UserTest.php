<?php

namespace Tests;

use App\database\migrations\UsersMigration;
use App\database\seeders\UsersSeeder;
use App\DatabaseConnect;
use App\models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private $config;
    private $connect;

    public function setUp(): void
    {
        $this->config = [
            'db_connection' => 'mysql',
            'db' => 'test',
            'user' => 'root',
            'password' => 'root',
            'host' => 'db',
            'port' => 3306
        ];

        $this->connect = DatabaseConnect::getConnect($this->config);
        $migrationUsers = new UsersMigration($this->config);
        $migrationUsers->up();
        $seederUsers = new UsersSeeder($this->config);
        $seederUsers->up();

        $users = [
            0 => ['name' => 'user1', 'email' => 'email1@user.ru', 'is_admin' => 0, 'password' => '12345'],
            1 => ['name' => 'user2', 'email' => 'email2@user.ru', 'is_admin' => 0, 'password' => '54321']
        ];

        array_walk($users, function ($user) {
            $queryText = "
                INSERT INTO users (name, email, is_admin, password)
                VALUES (:name, :email, :is_admin, :password)
            ";
            $statement = $this->connect->prepare($queryText);
            $statement->execute($user);
        });
    }

    public function testSave()
    {
        $name = 'usertest';
        $email = 'emailtest@test.ru';
        $isAdmin = false;
        $password = '987654';

        $user = new User(null, $this->config);
        $user->setName($name);
        $user->setEmail($email);
        $user->setIsAdmin($isAdmin);
        $user->setPassword($password);
        $id = $user->save()->getId();
        $selectedUser = $this->getUserById($id);

        $this->assertEquals($selectedUser->name, $name);
        $this->assertEquals($selectedUser->email, $email);
        $this->assertEquals($selectedUser->is_admin, (int)$isAdmin);

        $newUser = new User($id, $this->config);
        $this->assertEquals($name, $newUser->getName());
        $this->assertEquals($email, $newUser->getEmail());
        $this->assertEquals($isAdmin, $newUser->getIsAdmin());
    }

    public function testAllAndUpdate()
    {
        $user = new User(null, $this->config);
        $allUsers = $user->all();
        $user1 = new User(1, $this->config);
        $this->assertEquals($user1, $allUsers[0]);
        $user2 = $allUsers[1];
        $user2->setName('new name');
        $user2->save();
        $user3 = new User($user2->getId(), $this->config);
        $this->assertEquals('new name', $user3->getName());
    }

    public function testNewUser()
    {
        $user = new User(2, $this->config);
        $this->assertEquals('user1', $user->getName());
        $this->assertEquals('email1@user.ru', $user->getEmail());

        $this->expectException(\Exception::class);
        $failedUser = new User(10, $this->config);
    }

    public function testFind()
    {
        $user = new User(null, $this->config);
        $user->find(2);
        $this->assertEquals('user1', $user->getName());
        $this->assertEquals('email1@user.ru', $user->getEmail());

        $this->expectException(\Exception::class);
        $failedUser = new User(null, $this->config);
        $failedUser->find(10);
    }

    private function getUserById($id)
    {
        $queryRes = $this->connect->query("
                SELECT * from users where id = {$id}
        ");
        if ($queryRes && $queryRes->rowCount() > 0) {
            return $queryRes->fetchObject();
        }
        return null;
    }

    public function tearDown(): void
    {
        $migrationUsers = new UsersMigration($this->config);
        $migrationUsers->down();
    }
}
