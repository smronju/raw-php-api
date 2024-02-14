<?php

namespace App\Database;

use PDO;
use PDOException;

class DatabaseConnector
{
    private ?PDO $databaseConnection = null;

    public function __construct()
    {
        $host = $_ENV['DB_HOST'];
        $port = $_ENV['DB_PORT'];
        $database = $_ENV['DB_DATABASE'];
        $username = $_ENV['DB_USERNAME'];
        $password = $_ENV['DB_PASSWORD'];

        try {
            $this->databaseConnection = new PDO(
                "mysql:host=$host;port=$port;charset=utf8mb4;dbname=$database",
                $username,
                $password
            );
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function getConnection(): ?PDO
    {
        return $this->databaseConnection;
    }
}