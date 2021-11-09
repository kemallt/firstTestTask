<?php

namespace App;

use PDO;

class DatabaseConnect
{
    private static $connect = null;

    public static function getConnect(): PDO
    {
        if (static::$connect !== null) {
            return static::$connect;
        }

        $db_connection = DB_CONNECTION;
        $host = HOST;
        $port = PORT;
        $db = DB;
        $user = USER;
        $password = PASSWORD;

        try {
            $dsn = "$db_connection:host=$host;port=$port;dbname=$db;";
            static::$connect = new \PDO(
                $dsn,
                $user,
                $password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (\PDOException $e) {
            die($e->getMessage());
        }

        return static::$connect;
    }
}
