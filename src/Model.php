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
     * Массив атрибутов модели
     */
    protected array $attributes = [];

    /**
     * Массив имен атрибутов которые были изменены
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
     * Конструктор
     */
    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $attribute => $value) {
            $this->{$attribute} = $value;
        }
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
        $this->exists = true;
        $this->attributes = array_merge($this->attributes, $attributes);

        return $this;
    }

    /**
     * Добавляет модель в бд
     */
    protected function insert(): string
    {
        if (! $this->exists && $this->query()->insert($this->attributes) > 0) {
            $key = $this->primaryKey;

            if (! isset($this->attributes[$key])) {
                $this->{$this->key} = static::$db->getLastInsertId();
            }

            $this->exists = true;
            $this->changed = [];

            return $this->attributes[$key];
        }

        return '';
    }

    /**
     * Обновляет модель
     */
    protected function update(): string
    {
        if ($this->exists && ! empty($this->changed)) {
            $changed = [];

            foreach ($this->changed as $c) {
                $changed[$c] = $this->attributes[$c];
            }

            $key = $this->primaryKey;

            if ($this->query()->where($key, '=', $this->attributes[$key])->update($changed) > 0) {
                $this->changed = [];

                return $this->attributes[$key];
            }
        }

        return '';
    }

    /**
     * Сохраняет модель
     */
    public function save(): string
    {
        return $this->exists ? $this->update() : $this->insert();
    }

    /**
     * Удаляет модель
     */
    public function remove(): bool
    {
        if ($this->exists) {
            $key = $this->primaryKey;

            if ($this->query()->where($key, '=', $this->attributes[$key])->delete() > 0) {
                $this->exists = false;
                $this->attributes = [];
                $this->changed = [];

                return true;
            }
        }

        return false;
    }

    /**
     * Динамическая установка значения атрибута
     */
    public function __set(string $key, mixed $value): void
    {
        $key = Str::toSnake($key);

        $this->attributes[$key] = $value;

        if (! in_array($key, $this->changed)) {
            $this->changed[] = $key;
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
