<?php

namespace App\Services;

use PDO;
use PDOException;

class SubscriberService
{

    public function __construct(public $databaseConnection)
    {
    }

    public function all()
    {
        try {
            $statement = "SELECT id, first_name, last_name, email, status FROM subscribers;";
            $statement = $this->databaseConnection->query($statement);

            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            exit($exception->getMessage());
        }
    }

    public function find($email)
    {
        try {
            $statement = "SELECT id, first_name, last_name, email, status FROM subscribers WHERE email = ?;";
            $statement = $this->databaseConnection->prepare($statement);
            $statement->execute(array($email));

            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $exception) {
            exit($exception->getMessage());
        }
    }

    public function insert(array $data)
    {
        try {
            $statement = "INSERT INTO subscribers (first_name, last_name, email, status) VALUES (:firstname, :lastname, :email, :status);";
            $statement = $this->databaseConnection->prepare($statement);
            $statement->execute(
                array(
                    'firstname' => $data['first_name'],
                    'lastname' => $data['last_name'],
                    'email' => $data['email'],
                    'status' => $data['status'] ?? 0,
                )
            );

            return $statement->rowCount();
        } catch (PDOException $exception) {
            exit($exception->getMessage());
        }
    }

    public function paginate($perPage = 10): array
    {
        $page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
        $startAt = $perPage * ($page - 1);
        $total = $this->getCount();
        $totalPages = ceil($total / $perPage);

        try {
            $statement = "SELECT id, first_name, last_name, email, status FROM subscribers LIMIT $startAt, $perPage;";
            $statement = $this->databaseConnection->query($statement);
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            $response = ['data' => $result];

            if ($result) {
                $response['pagination'] = [
                    'current-page' => $page,
                    'per-page' => $perPage,
                    'from' => ($startAt != 0) ? $startAt : 1,
                    'to' => ($startAt + $perPage) > $total ? $total : ($startAt + $perPage),
                    'total' => $total,
                    'last-page' => $totalPages
                ];
            }

            return $response;
        } catch (PDOException $exception) {
            exit($exception->getMessage());
        }
    }

    public function getCount()
    {
        try {
            $sql = "select * from subscribers";
            $statement = $this->databaseConnection->query($sql);
            $statement->execute();

            return $statement->rowCount();
        } catch (PDOException $exception) {
            exit($exception->getMessage());
        }
    }


    public function seedSubscriber()
    {
        try {
            $this->dropTable();
            $this->createTable();
            $this->truncateTable();

            $sql = "INSERT INTO subscribers (first_name, last_name, email)
            VALUES
                ('John', 'Doe', 'johndoe@mailinator.com'),
                ('Jane', 'Doe', 'janedoe@mailinator.com'),
                ('John', 'Smith', 'johnsmith@mailinator.com'),
                ('Jenny', 'Doe', 'jennydoe@mailinator.com'),
                ('Johny', 'Doe', 'johnydoe@mailinator.com'),
                ('Joe', 'Doe', 'joedoe@mailinator.com'),
                ('Joy', 'Doe', 'joydoe@mailinator.com')";

            $statement = $this->databaseConnection->query($sql);
            return $statement->rowCount();
        } catch (PDOException $exception) {
            exit($exception->getMessage());
        }
    }

    public function dropTable()
    {
        try {
            $sql = "DROP TABLE IF EXISTS subscribers";

            $statement = $this->databaseConnection->query($sql);
            $statement->execute();
        } catch (PDOException $exception) {
            exit($exception->getMessage());
        }
    }


    public function createTable()
    {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS subscribers (
                id INT NOT NULL AUTO_INCREMENT,
                first_name VARCHAR(100) NOT NULL,
                last_name VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL,
                status SMALLINT DEFAULT 0,
                PRIMARY KEY (id)
            ) ENGINE=INNODB";

            $statement = $this->databaseConnection->query($sql);
            $statement->execute();
        } catch (PDOException $exception) {
            exit($exception->getMessage());
        }
    }

    public function truncateTable()
    {
        try {
            $sql = "TRUNCATE subscribers";

            $statement = $this->databaseConnection->query($sql);
            $statement->execute();
        } catch (PDOException $exception) {
            exit($exception->getMessage());
        }
    }
}