<?php

namespace First\Test\Task\DatabaseConnect;

use PDO;

function connect(): PDO
{
    require "/home/novapc74/firstTestTask/config/database.php";

    try {
        $dsn = "$db_connection:host=$host;port=$port;dbname=$db;";
        return  new \PDO(
            $dsn,
            $user,
            $password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    } catch (PDOException $e) {
        die($e->getMessage());
    }
}
