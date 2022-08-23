<?php
declare(strict_types=1);

namespace Manuylenko\DataBase;

abstract class Model
{
    /**
     * Экземпляр базы данных.
     */
    protected static DB $db;

    /**
     * Имя таблицы.
     */
    protected string $table = '';


    /**
     * Устанавливает экземпляр базы данных.
     */
    public static function setDB(DB $db): void
    {
        static::$db = $db;
    }

    /**
     * Получает экземпляр конструктора запроса.
     */
    public function query(): Query
    {
        return static::$db->getQueryInstance($this->getTable());
    }

    /**
     * Получает имя таблицы.
     */
    protected function getTable(): string
    {
        if (! $this->table) {
            $class = explode('\\', static::class);

            $this->table = substr($class[count($class)-1], 0, -5).'s';
        }

        return $this->table;
    }
}
