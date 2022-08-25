<?php
declare(strict_types=1);

namespace Manuylenko\DataBase;

use ArrayAccess;

abstract class Model implements ArrayAccess
{
    /**
     * Имя таблицы.
     */
    protected string $table = '';

    /**
     * Атрибуты модели.
     */
    protected array $attributes = [];

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
    protected function query(): Query
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
     * Устанавливает значение атрибута.
     */
    protected function setAttribute(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Получает значение атрибута.
     */
    protected function getAttribute(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Получает массив всех атрибутов.
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Проверяет существование атрибута.
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->attributes[$offset]);
    }

    /**
     * Получает значение атрибута.
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->getAttribute($offset);
    }

    /**
     * Устанавливает значение атрибута.
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->setAttribute($offset, $value);
    }

    /**
     * Удаляет атрибут.
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->attributes[$offset]);
    }

    /**
     * Динамическое получение значения атрибута.
     */
    public function __set(string $name, mixed $value): void
    {
         $this->setAttribute($name, $value);
    }

    /**
     * Динамическая установка значения атрибута.
     */
    public function __get(string $name): mixed
    {
        return $this->getAttribute($name);
    }
}
