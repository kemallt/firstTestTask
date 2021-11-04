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

function up()
{
    $conn = connect();
    $conn->query("
        CREATE TABLE users (
            id int PRIMARY KEY generated always as identity,
            name varchar(255) NOT null,
            email varchar(255) UNIQUE NOT null,
            is_admin boolean DEFAULT FALSE ,
            password varchar(60)
        )
    ");
}

function down()
{
    $conn = connect();
    $conn->query("DROP TABLE users");
}
