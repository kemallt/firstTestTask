<?php

function connect(): PDO
{
    require "/home/novapc74/firstTestTask/config/database.php";

    $dsn = "$db_connection:host=$host;port=$port;dbname=$db;";

    return new PDO(
        $dsn,
        $user,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
}

function seed()
{
    $conn = connect();

    $conn->query("
        INSERT INTO users (name, email, is_admin, password)
        VALUES ('admin', 'admin@admin.com', true, 'test')
        ");
}
