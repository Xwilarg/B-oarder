<?php

use Astylodon\Migrations\Migrate;
use Boarder\Database\Database;

require __DIR__ . '/../vendor/autoload.php';

$migrate = new Migrate(new Database(), "sqlite");

$migrate->findMigrations(__DIR__ . "/../Migrations");
$migrate->migrate();