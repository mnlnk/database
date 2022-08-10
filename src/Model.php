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
     * Устанавливает экземпляр базы данных.
     */
    public static function setDB(DB $db): void
    {
        static::$db = $db;
    }
}
