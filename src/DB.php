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
    protected static array $queries = [];

    /**
     * Идентификатор последней добавленной записи
     */
    protected string $lastInsertId = '0';


    /**
     * Конструктор.
     */
    public function __construct(
        protected Connector $connector
    ) {
        //
    }

    /**
     * Получает экземпляр конструктора запроса.
     */
    public function getQueryInstanceForTable(string $table): Query
    {
        return new Query($this, $table);
    }

    /**
     * Выполняет запрос.
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $pdo = $this->connector->getPdo();

        try {
            static::$queries[] = compact('sql', 'params');

            $pdo->beginTransaction();

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            $this->lastInsertId = $pdo->lastInsertId();

            $pdo->commit();

            return $stmt;
        }
        catch (PDOException $e) {
            $pdo->rollBack();

            throw new PDOException($e->getMessage().', SQL: '.$sql);
        }
    }

    /**
     * Получает идентификатор последней добавленной записи
     */
    public function getLastInsertId(): string
    {
        return $this->lastInsertId;
    }

    /**
     * Получает массив выполненных запросов.
     */
    public static function getQueries(): array
    {
        return static::$queries;
    }

    /**
     * Получает количество выполненных запросов.
     */
    public static function getCountQueries(): int
    {
        return count(static::$queries);
    }
}
