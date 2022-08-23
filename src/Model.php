<?php
declare(strict_types=1);

namespace Manuylenko\DataBase;

abstract class Model
{
    /**
     * Имя таблицы.
     */
    protected string $table = '';

    /**
     * Атрибуты модели.
     */
    protected array $attributes = [
        //
    ];

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

    /**
     * Получает экземпляр конструктора запроса.
     */
    public function query(): Query
    {
        if (! isset(static::$db)) {
            throw new DBException('Отсутствует экземпляр объекта базы данных.');
        }

        return static::$db->getQueryInstance($this->getTable());
    }

    /**
     * Получает имя таблицы.
     */
    protected function getTable(): string
    {
        if (! $this->table) {
            $name = explode('\\', static::class);
            $name = $name[count($name) - 1];
            $name = str_replace('Model', '', $name);
            $name = $name.(preg_match('#[+]s$#', $name) == 0 ? 's' : '');

            $this->table = $name;
        }

        return $this->table;
    }

    /**
     * Устанавливает атрибут.
     */
    protected function setAttribute(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Получает атрибут.
     */
    protected function getAttribute(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Получает все атрибуты.
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
