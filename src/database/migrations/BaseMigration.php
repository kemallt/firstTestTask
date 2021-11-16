<?php

namespace App\database\migrations;

use App\DatabaseConnect;

abstract class BaseMigration
{
    protected $connect;

    public function __construct($config = null)
    {
        $this->connect = DatabaseConnect::getConnect($config);
    }

    abstract function up();
    abstract function down();
}
