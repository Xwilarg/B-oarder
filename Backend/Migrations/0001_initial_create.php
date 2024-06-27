<?php

// 0001_initial_create.php

use Astylodon\Migrations\Database\DatabaseInterface;
use Astylodon\Migrations\Migration;

return new class implements Migration
{
    public function up(DatabaseInterface $database)
    {
        $database->exec("
            CREATE TABLE data (
                user_id     TEXT,
                id          TEXT,
                amount      REAL,
                PRIMARY KEY (user_id, id)
            )
        ");
    }
};