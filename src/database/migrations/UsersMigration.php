<?php

namespace App\database\migrations;

class UsersMigration extends BaseMigration
{
    public function up()
    {
        $this->connect->query("
            CREATE TABLE IF NOT EXISTS users (
                id int PRIMARY KEY AUTO_INCREMENT,
                name varchar(255) UNIQUE NOT null,
                email varchar(255) UNIQUE NOT null,
                is_admin boolean DEFAULT FALSE ,
                password varchar(255)
            )
        ");
    }

    public function down()
    {
        $this->connect->query("DROP TABLE IF EXISTS users");
    }
}
