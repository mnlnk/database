<?php
declare(strict_types=1);

namespace Manuylenko\DataBase\Connectors;

use PDO;

interface Connector
{
    /**
     * Получает экземпляр PDO.
     */
    public function getPdo(): PDO;
}
