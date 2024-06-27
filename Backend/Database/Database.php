<?php

namespace Boarder\Database;

use Astylodon\Migrations\Database\DatabaseInterface;

class Database implements DatabaseInterface
{
    private \PDO $conn;

    public function __construct()
    {
        $this->conn = new \PDO("sqlite:db.sqlite");
    }

    public function exec(string $query, mixed ...$params)
    {
        $sth = $this->conn->prepare($query);
        $sth->execute($params);
    }

    public function get(string $query, mixed ...$params)
    {
        $sth = $this->conn->prepare($query);
        $sth->execute($params);

        return $sth->fetch(\PDO::FETCH_OBJ);
    }

    public function getAll(string $query, mixed ...$params)
    {
        $sth = $this->conn->prepare($query);
        $sth->execute($params);

        return $sth->fetchAll(\PDO::FETCH_OBJ);
    }
}