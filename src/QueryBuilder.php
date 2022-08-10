<?php
declare(strict_types=1);

namespace Manuylenko\DataBase;

abstract class QueryBuilder
{
    /**
     * Столбцы.
     */
    protected array $columns = [];

    /**
     * Условия выборки.
     */
    protected array $where = [];

    /**
     * Присоединения.
     */
    protected array $join = [];

    /**
     * Сортировки.
     */
    protected array $order = [];

    /**
     * Группировки.
     */
    protected array $group = [];

    /**
     * Условия группировки.
     */
    protected array $having = [];

    /**
     * Лимит.
     */
    protected int $limit = 0;

    /**
     * Смещение.
     */
    protected int $offset = 0;

    /**
     * Параметры.
     */
    protected array $bind = [];


    /**
     * Получает имя таблицы.
     */
    abstract protected function getTable(): string;


    /**
     * Извлекает параметры и очищает данные запроса.
     */
    protected function takeParams(): array
    {
        $params = $this->bind;

        $this->columns = [];
        $this->where = [];
        $this->join = [];
        $this->order = [];
        $this->group = [];
        $this->having = [];
        $this->limit = 0;
        $this->offset = 0;
        $this->bind = [];

        return $params;
    }

    /**
     * Собирает параметры запроса.
     */
    protected function buildParams(): string
    {
        $sql = '';

        if (count($this->join)) {
            $sql .= ' ' . implode(' ', $this->join);
        }

        if (count($this->where)) {
            $sql .= ' '.implode(' ', $this->where);
        }

        if (count($this->group)) {
            $sql .= ' GROUP BY '.implode(', ', $this->group);

            if (count($this->having)) {
                $sql .= ' '.implode(' ', $this->having);
            }
        }

        if (count($this->order)) {
            $sql .= ' ORDER BY '.implode(', ', $this->order);
        }

        if ($this->limit) {
            $sql .= ' LIMIT '.$this->limit;

            if ($this->offset) {
                $sql .= ' OFFSET '.$this->offset;
            }
        }

        return $sql;
    }

    /**
     * Собирает запрос INSERT INTO.
     */
    protected function buildInsert(array $row): string
    {
        $keys = implode(', ', array_keys($row));

        $values = implode(', ', array_fill(0, count($row), '?'));

        $this->bind = array_values($row);

        return 'INSERT INTO '.$this->getTable().' ('.$keys.') VALUES ('.$values.')';
    }

    /**
     * Собирает запрос SELECT.
     */
    protected function buildSelect(): string
    {
        $columns = count($this->columns) ? implode(', ', $this->columns) : '*';

        return 'SELECT '.$columns.' FROM '.$this->getTable().$this->buildParams();
    }

    /**
     * Собирает запрос UPDATE.
     */
    protected function buildUpdate(array $row): string
    {
        $update = implode(', ', array_map(fn ($column) => $column.' = ?', array_keys($row)));

        $this->bind = array_merge(array_values($row), $this->bind);

        return 'UPDATE '.$this->getTable().' SET '.$update.$this->buildParams();
    }

    /**
     * Собирает запрос DELETE.
     */
    protected function buildDelete(): string
    {
        return 'DELETE FROM '.$this->getTable().$this->buildParams();
    }

    /**
     * Указывает колонки.
     */
    public function columns(array $columns): static
    {
        $this->columns = array_merge($this->columns, $columns);

        return $this;
    }

    /**
     * Указывает колонку.
     */
    public function column(string $column): static
    {
        return $this->columns([$column]);
    }

    /**
     * Указывает условия выборки.
     */
    public function where(
        string $column,
        string $operator,
        string|int|null $value,
        string $predicate = 'AND'
    ): static
    {
        $this->where[] = (count($this->where) ? $predicate.' ' : 'WHERE ').$column.' '.$operator.' ?';

        $this->bind[] = $value;

        return $this;
    }

    /**
     * Указывает условия выборки.
     */
    public function whereBetween(
        string $column,
        int|string $min,
        int|string $max,
        bool $not = false,
        string $predicate = 'AND'
    ): static
    {
        $this->where[] = (count($this->where) ? $predicate.' ' : 'WHERE ').$column.' '.($not ? 'NOT ' : '').'BETWEEN ? AND ?';

        $this->bind = array_merge($this->bind, [$min, $max]);

        return $this;
    }

    /**
     * Указывает условия выборки.
     */
    public function whereLike(
        string $column,
        string $value,
        bool $not = false,
        string $predicate = 'AND'
    ): static
    {
        return $this->where($column, ($not ? 'NOT ' : '').'LIKE', $value, $predicate);
    }

    /**
     * Указывает условия выборки.
     */
    public function whereIn(
        string $column,
        array $values,
        bool $not = false,
        string $predicate = 'AND'
    ): static
    {
        $strValues = implode(', ', array_fill(0, count($values), '?'));

        $this->where[] = (count($this->where) ? $predicate.' ' : 'WHERE ').$column.' '.($not ? 'NOT ' : '').'IN ('.$strValues.')';

        $this->bind = array_merge($this->bind, array_values($values));

        return $this;
    }

    /**
     * Указывает присоединение.
     */
    public function join(
        string $table,
        string $left,
        string $operator,
        string $right,
        string $type = 'INNER'
    ): static
    {
        $this->join[] = strtoupper($type).' JOIN '.$table.' ON '.$left.' '.$operator.' '.$right.'';

        return $this;
    }

    /**
     * Указывает режим сортировки.
     */
    public function order(string $column, string $mode = ''): static
    {
        $this->order[] = $column.($mode ? ''.strtoupper(' '.$mode): '');

        return $this;
    }

    /**
     * Указывает группировку по столбцу.
     */
    public function group(string $column): static
    {
        $this->group[] = $column;

        return $this;
    }

    /**
     * Указывает условия выборки для группы.
     */
    public function having(
        string $column,
        string $operator,
        string|int $value,
        string $predicate = 'AND'
    ): static
    {
        $this->having[] = (count($this->having) ? $predicate.' ' : 'HAVING ').$column.' '.$operator.' ?';

        $this->bind[] = $value;

        return $this;
    }

    /**
     * Указывает количество возвращаемых результатов.
     */
    public function limit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Указывает смещение начала выборки.
     */
    public function offset(int $offset): static
    {
        $this->offset = $offset;

        return $this;
    }
}
