<?php

namespace Tests;

use App\database\migrations\TasksMigration;
use App\database\migrations\UsersMigration;
use App\database\seeders\UsersSeeder;
use App\DatabaseConnect;
use App\Models\Task;
use App\Models\User;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    private $connect;

    public function setUp(): void
    {
        $config = [
            'db_connection' => 'mysql',
            'db' => 'testtest',
            'user' => 'root',
            'password' => '129354',
            'host' => 'localhost',
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
        
        $tasks = [
            0 => ['user_id' => 2, 'description' => 'task 1 of user1', 'is_done' => 0, 'is_edits_by_admin' => 0],
            1 => ['user_id' => 2, 'description' => 'task 2 of user1', 'is_done' => 0, 'is_edits_by_admin' => 0],
            2 => ['user_id' => 3, 'description' => 'task 1 of user1', 'is_done' => 0, 'is_edits_by_admin' => 0],
            3 => ['user_id' => 3, 'description' => 'task 2 of user1', 'is_done' => 0, 'is_edits_by_admin' => 0],
            4 => ['user_id' => 1, 'description' => 'task 1 of admin user', 'is_done' => 0, 'is_edits_by_admin' => 0]
        ];
        array_walk($tasks, function ($task) {
            $queryText = "
                INSERT INTO tasks (user_id, description, is_done, is_edits_by_admin)
                VALUES (:user_id, :description, :is_done, :is_edits_by_admin)
            ";
            $statement = $this->connect->prepare($queryText);
            $statement->execute($task);
        });
    }

    public function testSave()
    {
        $userId = 1;
        $user = User::find($userId);
        $description = 'added task for user 1';

        $task = new Task();
        $task->setUser($user);
        $task->setDescription($description);
        $id = $task->save()->getId();
        $selectedTask = $this->getTaskById($id);

        $this->assertEquals($description, $selectedTask->description);
        $this->assertEquals(1, $selectedTask->user_id);
        $this->assertEquals(false, (bool)$selectedTask->is_done);

        $newTask = new Task($id);
        $this->assertEquals($description, $newTask->getDescription());
        $this->assertEquals($user, $newTask->getUser());
        $this->assertEquals(false, $newTask->getIsDone());
    }

    public function testAllAndUpdate()
    {
        $allTasks = Task::all();
        usort($allTasks, fn ($task1, $task2) => $task1->getId() > $task2->getId());
        $task1 = new Task(1);
        $this->assertEquals($task1, $allTasks[0]);
        $task2 = $allTasks[1];
        $task2->setDescription('new description for task2');
        $task2->save();
        $task3 = new Task($task2->getId());
        $this->assertEquals('new description for task2', $task3->getDescription());
    }

    public function testNewTask()
    {
        $task = new Task(2);
        $this->assertEquals('task 2 of user1', $task->getDescription());
        $this->assertEquals(false, $task->getIsDone());

        $this->expectException(\Exception::class);
        new Task(100);
    }

    public function testFind()
    {
        $task = Task::find(4);
        $this->assertEquals('task 2 of user1', $task->getDescription());
        $this->assertEquals(3, $task->getUser()->getId());

        $this->expectException(\Exception::class);
        Task::find(100);
    }

    private function getTaskById($id)
    {
        $queryRes = $this->connect->query("
                SELECT * from tasks where id = {$id}
        ");
        if ($queryRes && $queryRes->rowCount() > 0) {
            return $queryRes->fetchObject();
        }
        return null;
    }

    public function tearDown(): void
    {
        $migrationTasks = new TasksMigration();
        $migrationTasks->down();
        $migrationUsers = new UsersMigration();
        $migrationUsers->down();
    }
}
