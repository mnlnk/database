<?php
declare(strict_types=1);

namespace Manuylenko\DataBase;

use PDO;

class Table extends QueryBuilder
{
    /**
     * Тип возвращаемого результата.
     */
    protected array $fetchMode = [];


    /**
     * Конструктор.
     */
    public function __construct(
        protected DB $db,
        protected string $name
    ) {
        //
    }

    /**
     * Получает имя таблицы.
     */
    protected function getTable(): string
    {
        return $this->name;
    }

    /* -- */

    /**
     * INSERT INTO.
     */
    public function insert(array $row): int
    {
        return $this->db->query($this->buildInsert($row), $this->takeParams())->rowCount();
    }

    /**
     * SELECT.
     */
    public function get(): mixed
    {
        $stmt = $this->db->query($this->buildSelect(), $this->takeParams());

        if ($this->fetchMode) {
            $stmt->setFetchMode(...$this->fetchMode);
        }

        return $stmt->fetch();
    }

    /**
     * SELECT.
     */
    public function select(): array
    {
        $stmt = $this->db->query($this->buildSelect(), $this->takeParams());

        if ($this->fetchMode) {
            $stmt->setFetchMode(...$this->fetchMode);
        }

        return $stmt->fetchAll();
    }

    /**
     * UPDATE.
     */
    public function update(array $row): int
    {
        return $this->db->query($this->buildUpdate($row), $this->takeParams())->rowCount();
    }

    /**
     * DELETE.
     */
    public function delete(): int
    {
        return $this->db->query($this->buildDelete(), $this->takeParams())->rowCount();
    }

    /* -- */

    /**
     * Указывает тип возвращаемого результата.
     */
    public function fetchClass(string $class, ?array $constructorArgs = null): static
    {
        $this->fetchMode = [PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $class, $constructorArgs];

        return $this;
    }

    /**
     * Указывает тип возвращаемого результата.
     */
    public function fetchColumn(int $column): static
    {
        $this->fetchMode = [PDO::FETCH_COLUMN, $column];

        return $this;
    }

    /**
     * Указывает тип возвращаемого результата.
     */
    public function fetchAssoc(): static
    {
        $this->fetchMode = [PDO::FETCH_ASSOC];

        return $this;
    }

    /**
     * Указывает тип возвращаемого результата.
     */
    public function fetchNumeric(): static
    {
        $this->fetchMode = [PDO::FETCH_NUM];

        return $this;
    }

    /**
     * Указывает тип возвращаемого результата.
     */
    public function fetchObject(): static
    {
        $this->fetchMode = [PDO::FETCH_OBJ];

        return $this;
    }
}
