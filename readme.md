## DataBase 0.3.1

##### Требования:

+ `PHP >= 8.0`
+ `ext-pdo`


##### Установка:

```
composer require mnlnk/database
```


##### Пример:

###### Создание экземпляра БД

```php
use Manuylenko\DataBase\Connectors\MySQLConnector;
use Manuylenko\DataBase\DB;

$name = 'database';
$user = 'mnlnk';
$passwd = 'LvQ_]uP.OfxE!kFp';
$host = 'localhost';

$connector = new MySQLConnector($user, $passwd, $name, $host);
$db = new DB($connector);
```

###### Создание простых SQL запросов

```php
// Получение одной записи
$stmt = $db->query('SELECT Album FROM Artists WHERE Singer = ?', [
    'The Prodigy'
]);

$row = $stmt->fetch();
```

```php
// Получение массива записей
$stmt = $db->query('SELECT * FROM Artists');

$rows = $stmt->fetchAll();
```

```php
// Добавление записи 
$db->query('INSERT INTO * FROM Artists', [
    'Singer' => 'The Prodigy',
    'Album' => 'Music For The Jilted Generation',
    'Year' => '1994',
    'Sale' => 1500000
]);
```

###### Создание запросов с помощью конструктора

```php
// Получение экземпляра запроса для таблицы
$table = $db->getQueryInstanceForTable('Artists');
```

```php
// Добавление записи в таблицу
$table->insert([
    'Singer' => 'The Prodigy',
    'Album' => 'Music For The Jilted Generation',
    'Year' => '1994',
    'Sale' => 1500000
]);
```

```php
// Получение массива записей
$rows = $table 
    ->column('Singer')
    ->where('Year', '>=', '1994')
    ->group('Singer')
    ->fetchColumn(0)
    ->select(); 
```

```php
// Получение одной записи
$row = $table
    ->column('Singer')
    ->where('Year', '>=', '2000')
    ->get();
```

```php
// Обновление записи
$table
    ->where('Id', '=', '73')
    ->update([
        'Singer' => 'Massive Attack',
    ]);
```

```php
// Удаление записи
$table
    ->where('Id', '=', '73')
    ->delete();
```

###### Дополнительные возможности

```php
// Получение списка выполненных запросов
$queries = DB::getQueries();
```

```php
// Получение количество выполненных запросов
$countQueries = DB::getCountQueries();
```
