<?php

namespace App;

use PDO;

class DatabaseConnect
{
    private static $connect = null;

    public static function getConnect($config = null): PDO
    {
        if (static::$connect !== null) {
            return static::$connect;
        }

        if ($config === null) {
            $db_connection = DB_CONNECTION;
            $host = HOST;
            $port = PORT;
            $db = DB;
            $user = USER;
            $password = PASSWORD;
        } else {
            $db_connection = $config['db_connection'];
            $host = $config['host'];
            $port = $config['port'];
            $db = $config['db'];
            $user = $config['user'];
            $password = $config['password'];
        }

        $dsn = "$db_connection:host=$host;port=$port;dbname=$db";

        try {
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
