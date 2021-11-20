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

    abstract public function up();
    abstract public function down();
}
