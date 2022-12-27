<?php
declare(strict_types=1);

namespace Manuylenko\DataBase;

use Manuylenko\StringHelper\Str;

abstract class Model
{
    /**
     * Имя таблицы
     */
    protected string $table = '';

    /**
     * Массив атрибутов модели.
     */
    protected array $attributes = [];

    /**
     * Массив атрибутов которые были изменены
     */
    protected array $changed = [];

    /**
     * Экземпляр базы данных
     */
    protected static DB $db;


    /**
     * Устанавливает экземпляр базы данных
     */
    public static function setDB(DB $db): void
    {
        static::$db = $db;
    }

    /**
     * Получает экземпляр запроса для таблицы модели
     */
    protected function query(): Query
    {
        if (! isset(static::$db)) {
            throw new DBException('Отсутствует экземпляр объекта базы данных.');
        }

        return static::$db->getQueryInstanceForTable($this->getTable());
    }

    /**
     * Получает имя таблицы
     */
    protected function getTable(): string
    {
        if (! $this->table) {
            $name = str_replace('Model', '', basename(static::class, 's')).'s';

            $this->table = Str::toSnake($name);
        }

        return $this->table;
    }

    /**
     * Устанавливает значение атрибута
     */
    protected function setAttribute(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;

        if (! in_array($key, $this->changed)) {
            $this->changed[] = $key;
        }
    }

    /**
     * Получает значение атрибута
     */
    protected function getAttribute(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Получает массив всех атрибутов
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Динамическое получение значения атрибута
     */
    public function __set(string $name, mixed $value): void
    {
         $this->setAttribute(Str::toSnake($name), $value);
    }

    /**
     * Динамическая установка значения атрибута
     */
    public function __get(string $name): mixed
    {
        return $this->getAttribute(Str::toSnake($name));
    }
}
