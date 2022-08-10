<?php
declare(strict_types=1);

namespace Manuylenko\DataBase;

use Manuylenko\DataBase\Connectors\Connector;
use PDOException;
use PDOStatement;

class DB
{
    /**
     * Массив выполненных запросов.
     */
    protected array $queries = [];


    /**
     * Конструктор.
     */
    public function __construct(
        protected Connector $connector
    ) {
        //
    }

    /**
     * Получает экземпляр таблицы.
     */
    public function table(string $name): Table
    {
        return new Table($this, $name);
    }

    /**
     * Выполняет запрос.
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $pdo = $this->connector->getPdo();

        try {
            $this->queries[] = compact('sql', 'params');

            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $pdo->commit();

            return $stmt;
        }
        catch (PDOException $e) {
            $pdo->rollBack();

            throw new PDOException($e->getMessage().', SQL: '.$sql);
        }
    }

    /**
     * Получает массив выполненных запросов.
     */
    public function getQueries(): array
    {
        return $this->queries;
    }
}
