<?php

namespace App\database\migrations;

class TasksMigration extends BaseMigration
{
    public function up()
    {
        $this->connect->query("
            CREATE TABLE IF NOT EXISTS tasks (
                id int PRIMARY KEY AUTO_INCREMENT,
                user_id int NOT NULL REFERENCES users(id),
                description text NOT NULL,
                is_done boolean DEFAULT FALSE,
                is_edits_by_admin boolean DEFAULT FALSE,
                foreign key (user_id) references users(id) on delete cascade
            )
        ");
    }

    public function down()
    {
        $this->connect->query("DROP TABLE IF EXISTS tasks");
    }
}
