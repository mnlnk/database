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
     * Получает имя таблицы.
     */
    abstract protected function getTableName(): string;

    /**
     * Устанавливает экземпляр базы данных.
     */
    public static function setDB(DB $db): void
    {
        static::$db = $db;
    }

    /**
     * Получает экземпляр таблицы.
     */
    public function table(): Table
    {
        return static::$db->table($this->getTableName());
    }
}
