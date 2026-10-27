<?php

namespace SlmQueueDoctrineTest\Util;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

/**
 * Creates a DBAL connection to an in-memory SQLite database
 */
class ConnectionFactory
{
    public function __invoke(): Connection
    {
        return DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ]);
    }
}
