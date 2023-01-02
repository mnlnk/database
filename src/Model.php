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
     * Первичный ключ
     */
    protected string $primaryKey = 'id';

    /**
     * Индикатор существования модели
     */
    protected bool $exists = false;

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
     * Загружает модель
     */
    protected function load(array $attributes): static
    {
       $this->attributes = array_merge($this->attributes, $attributes);

       $this->exists = true;

       return $this;
    }

    /**
     * Добавляет модель в бд
     */
    protected function insert(): bool
    {
        return ! $this->exists && $this->query()->insert($this->attributes) > 0;
    }

    /**
     * Обновляет модель
     */
    protected function update(): bool
    {
        $key = $this->primaryKey;

        return $this->exists && $this->query()->where($key, '=', $this->attributes[$key])->update($this->changed) > 0;
    }

    /**
     * Сохраняет модель
     */
    public function save(): bool
    {
        return $this->exists ? $this->update() : $this->insert();
    }

    /**
     * Удаляет модель
     */
    public function remove(): bool
    {
        $key = $this->primaryKey;

        return $this->exists && $this->query()->where($key, '=', $this->attributes[$key])->delete() > 0;
    }

    /**
     * Динамическая установка значения атрибута
     */
    public function __set(string $key, mixed $value): void
    {
        $key = Str::toSnake($key);

        if ($key == $this->primaryKey) {
            throw new ModelException(sprintf('Первичный ключ "%s" нельзя редактировать', $key));
        }

        $this->attributes[$key] = $value;

        if (! in_array($key, $this->changed)) {
            $this->changed[$key] = $value;
        }
    }

    /**
     * Динамическое получение значения атрибута
     */
    public function __get(string $key): mixed
    {
        return $this->attributes[Str::toSnake($key)] ?? null;
    }
}
