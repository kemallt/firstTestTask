<?php

namespace App\database\seeders;

use App\database\migrations\BaseMigration;

class UsersSeeder extends BaseMigration
{
    function up()
    {
        $userAdminExists = $this->connect->query("
            SELECT * FROM users WHERE email = 'admin@admin.com'
        ")->rowCount() > 0;
        if ($userAdminExists) {
            $this->connect->query("
                UPDATE users SET is_admin = true where email = 'admin@admin.com'
            ");
            return;
        }
        $password = password_hash('123', PASSWORD_DEFAULT);
        $this->connect->query("
            INSERT INTO users (name, email, is_admin, password)
            VALUES ('admin', 'admin@admin.com', true, '{$password}')
            ");
    }

    function down()
    {
        $this->connect->query("
            DELETE FROM users;
        ");
    }
}
