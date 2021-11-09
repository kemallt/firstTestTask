<?php

namespace App\database\migrations;

use App\DatabaseConnect;

abstract class BaseMigration
{
    protected $connect;

    public function __construct()
    {
        $this->connect = DatabaseConnect::getConnect();
    }

    abstract function up();
    abstract function down();
}
