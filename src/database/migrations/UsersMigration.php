<?php

namespace App\database\migrations;

class UsersMigration extends BaseMigration
{
    function up()
    {
        $this->connect->query("
            CREATE TABLE users (
                id int PRIMARY KEY AUTO_INCREMENT,
                name varchar(255) NOT null,
                email varchar(255) UNIQUE NOT null,
                is_admin boolean DEFAULT FALSE ,
                password varchar(60)
            )
        ");
    }

    function down()
    {
        $this->connect->query("DROP TABLE users");
    }
}