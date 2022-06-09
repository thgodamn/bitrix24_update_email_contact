<?php

namespace sd;

class DB
{
    protected string $user = 'root';
    protected string $password = 'root';
    protected string $host = 'localhost';
    protected string $dbName = 'work-with-bitrix';
    protected string $bitrixUsersTable = 'bitrix_users';
    protected \PDO $connection;

    public function __construct()
    {
        $this->createConnection();
    }

    protected function createConnection()
    {
        try {
            $this->connection = new \PDO(
                "mysql:host={$this->host};dbname={$this->dbName}",
                $this->user,
                $this->password);
        } catch (\PDOException $e) {
            echo "PDO error: {$e->getMessage()}";
            die();
        }
    }

    public function storeUsers(array $users)
    {
        if ( ! empty($users)) {
            foreach ($users as $user) {
                $sql  = "INSERT INTO {$this->bitrixUsersTable} (user_id, emails) VALUES (?,?)";
                $stmt = $this->connection->prepare($sql);
                $stmt->execute(array($user['id'], $user['emails'] ?? null));
            }
        }
    }
}
