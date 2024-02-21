## DataBase 0.4.0

#### Требования:

+ `PHP >= 8.0`
+ `ext-pdo`


#### Установка:

```
composer require mnlnk/database
```


#### Пример:

##### Создание экземпляра БД

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

##### Создание простых SQL запросов

```php
// Получает одну запись
$stmt = $db->query('SELECT Album FROM Artists WHERE Singer = ?', [
    'The Prodigy'
]);

$row = $stmt->fetch();
```

```php
// Получает массив записей из таблицы
$stmt = $db->query('SELECT * FROM Artists');

$rows = $stmt->fetchAll();
```

```php
// Добавляет запись в таблицу
$db->query('INSERT INTO * FROM Artists', [
    'Singer' => 'The Prodigy',
    'Album' => 'Music For The Jilted Generation',
    'Year' => '1994',
    'Sale' => 1500000
]);
```

##### Создание запросов с помощью конструктора

```php
// Получает экземпляр запроса для таблицы
$table = $db->getQueryInstanceForTable('Artists');
```

```php
// Добавляет запись в таблицу
$table->insert([
    'Singer' => 'The Prodigy',
    'Album' => 'Music For The Jilted Generation',
    'Year' => '1994',
    'Sale' => 1500000
]);
```

```php
// Получает массив записей из таблицы
$rows = $table 
    ->column('Singer')
    ->where('Year', '>=', '1994')
    ->group('Singer')
    ->fetchColumn(0)
    ->select(); 
```

```php
// Получает одну запись из таблицы
$row = $table
    ->column('Singer')
    ->where('Year', '>=', '2000')
    ->get();
```

```php
// Обновляет запись в таблице
$table
    ->where('Id', '=', '73')
    ->update([
        'Singer' => 'Massive Attack',
    ]);
```

```php
// Удаляет запись из таблицы
$table
    ->where('Id', '=', '73')
    ->delete();
```

##### Дополнительные возможности

```php
// Получает список выполненных запросов
$queries = DB::getQueries();
```

```php
// Получает количество выполненных запросов
$countQueries = DB::getCountQueries();
```
