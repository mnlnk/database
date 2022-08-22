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
     * Получает имя таблицы.
     */
    protected function getTableName(): string
    {
        if (! $this->table) {
            $class = explode('\\', static::class);

            $this->table = substr($class[count($class)-1], 0, -5).'s';
        }

        return $this->table;
    }

    /**
     * Получает экземпляр таблицы.
     */
    public function table(): Table
    {
        return static::$db->table($this->getTableName());
    }
}
