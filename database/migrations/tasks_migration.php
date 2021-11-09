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
        CREATE TABLE tasks (
            id int PRIMARY KEY generated always AS identity,
            user_id int NOT NULL REFERENCES users(id),
            description text NOT NULL,
            is_done boolean DEFAULT FALSE,
            is_edits_by_admin boolean DEFAULT FALSE
        )
    ");
}

function down()
{
    $conn = connect();
    $conn->query("DROP TABLE tasks");
}
