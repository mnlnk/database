## DataBase 0.2.1

##### Требования:

+ `PHP >= 8.0`
+ `ext-pdo`


##### Установка:

```
composer require mnlnk/database
```


##### Пример:

```php
use Manuylenko\DataBase\Connectors\MySQLConnector;
use Manuylenko\DataBase\DB;

$dbname = 'database';
$user = 'mnlnk';
$passwd = 'LvQ_]uP.OfxE!kFp';
$host = 'localhost';

$connector = new MySQLConnector($user, $passwd, $dbname, $host);

$db = new DB($connector);
```

```php
/**
 * Простые SQL запросы.
 */

$array = $db->query("SELECT * FROM Artists")->fetchAll();
```

```php
/**
 * Конструктор запросов.
 */

$table = $db->table('Artists');

// INSERT INTO
$table->insert([
    'Singer' => 'The Prodigy',
    'Album' => 'Music For The Jilted Generation',
    'Year' => '1994',
    'Sale' => 1500000
]);

// SELECT
$array = $table
    ->column('Singer')
    ->where('Year', '>=', '1994')
    ->group('Singer')
    ->fetchColumn(0)
    ->select();
    
// SELECT
$value = $table
    ->column('Singer')
    ->where('Year', '>=', '2000')
    ->get();


// UPDATE
$table
    ->where('Id', '=', '73')
    ->update([
        'Singer' => 'Massive Attack',
    ]);

// DELETE
$table
    ->where('Id', '=', '73')
    ->delete();
```
