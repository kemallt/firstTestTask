<?php

namespace App\database\migrations;

class TasksMigration extends BaseMigration
{
    function up()
    {
        $this->connect->query("
            CREATE TABLE tasks (
                id int PRIMARY KEY AUTO_INCREMENT,
                user_id int NOT NULL REFERENCES users(id),
                description text NOT NULL,
                is_done boolean DEFAULT FALSE,
                is_edits_by_admin boolean DEFAULT FALSE
            )
        ");
    }

    function down()
    {
        $this->connect->query("DROP TABLE tasks");
    }
}
