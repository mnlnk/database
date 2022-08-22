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
     * Конструктор.
     */
    public function __construct()
    {
        if (! $this->table) {
            $n = explode('\\', static::class);
            $n = $n[count($n)-1];

            $this->table = substr($n, 0, -5).'s';
        }
    }

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
        return static::$db->table($this->table);
    }
}
