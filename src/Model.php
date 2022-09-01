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
     * Массив атрибутов.
     */
    protected array $attributes = [];

    /**
     * Массив имен атрибутов которые были изменены.
     */
    protected array $changed = [];

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
            $name = str_replace('Model', '', basename(static::class, 's')).'s';

            $this->table = $this->toSnake($name);
        }

        return $this->table;
    }

    /**
     * Устанавливает значение атрибута.
     */
    protected function setAttribute(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;

        $this->changed[$key] = true;
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
     * Корректирует имя атрибута.
     */
    protected function toSnake(string $name): string
    {
        return strtolower(ltrim(preg_replace('/[A-Z]/', '_$0', $name), '_'));
    }

    /**
     * Динамическое получение значения атрибута.
     */
    public function __set(string $name, mixed $value): void
    {
         $this->setAttribute($this->toSnake($name), $value);
    }

    /**
     * Динамическая установка значения атрибута.
     */
    public function __get(string $name): mixed
    {
        return $this->getAttribute($this->toSnake($name));
    }
}
