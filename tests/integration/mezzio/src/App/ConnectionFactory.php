<?php

namespace App;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class ConnectionFactory
{
    public function __invoke(): Connection
    {
        return DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path'   => __DIR__ . '/../../temp/database.sqlite',
        ]);
    }
}
