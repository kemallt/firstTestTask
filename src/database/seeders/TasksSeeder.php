<?php

namespace App\database\seeders;

class TasksSeeder extends \App\database\migrations\BaseMigration
{

    function up()
    {
        $userRes = $this->connect->query("
            SELECT * FROM users LIMIT 1
        ");
        if ($userRes && $userRes->rowCount()) {
            $userId = $userRes->fetchObject()->id;
        } else {
            $password = password_hash('12345', PASSWORD_DEFAULT);
            $this->connect->query("
                INSERT INTO users (name, email, is_admin, password)
                VALUES ('user1', 'user1@user.com', false, '{$password}')
            ");
            $userId = $this->connect->lastInsertId();
        }
        for ($i = 1; $i <= 10; $i++) {
            $descritption = "description of task №{$i}";
            $this->connect->query("
                INSERT INTO tasks (user_id, description)
                VALUES ({$userId}, '{$descritption}')
            ");
        }
    }

    function down()
    {
        $this->connect->query("
            DELETE FROM tasks;
        ");
    }
}