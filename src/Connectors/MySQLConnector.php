<?php
declare(strict_types=1);

namespace Manuylenko\DataBase\Connectors;

use PDO;

class MySQLConnector implements Connector
{
    /**
     * Экземпляр PDO.
     */
    protected PDO $pdo;


    /**
     * Конструктор.
     */
    public function __construct(
        string $username,
        string $password,
        string $dbname,
        string $host = 'localhost',
        ?string $port = null,
        ?string $charset = null
    ) {
        $dsn = 'mysql:dbname='.$dbname.';host='.$host;

        if ($port) {
            $dsn .= ';port='.$port;
        }

        if ($charset) {
            $dsn .= ';charset='.$charset;
        }

        $this->pdo = new PDO($dsn, $username, $password);

        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, false);
    }

    /**
     * Получает экземпляр PDO.
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}
