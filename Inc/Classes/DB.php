<?php

namespace sd;

class DB
{
    protected string $user = 'root';
    protected string $password = '';
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

    public function clearEmailUsers($regex)
    {
        $sql = "SELECT * FROM {$this->bitrixUsersTable}";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        $fp = fopen('clearUsers.txt', 'wa+');

        while ($row = $stmt->fetch()) {

            $emails = explode(',',$row['emails']);
            $isInvalidEmail = false;

            foreach ($emails as $key => $email) {
                $matches = array();
                if (preg_match($regex, $email, $matches)) {
                    unset($emails[$key]);
                    $isInvalidEmail = true;
                }
            }
            if ($isInvalidEmail)
                fwrite($fp, serialize(['ID' => $row['id'], 'EMAILS' => implode($emails)]) . PHP_EOL);
        }
        fclose($fp);
    }
}
