<?php

namespace App\database\seeders;

use App\database\migrations\BaseMigration;

class UserSeeder extends BaseMigration
{
    function up()
    {
        $this->connect->query("
            INSERT INTO users (name, email, is_admin, password)
            VALUES ('admin', 'admin@admin.com', true, 'test')
            ");
    }

    function down()
    {
    // TODO: Implement down() method.
    }
}
