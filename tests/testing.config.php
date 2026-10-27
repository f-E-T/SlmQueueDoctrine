<?php

use SlmQueue\Strategy\MaxRunsStrategy;
use SlmQueueDoctrine\Factory\DoctrineQueueFactory;
use SlmQueueDoctrineTest\Util\ConnectionFactory;

return [
    'dependencies' => [
        'factories' => [
            'doctrine.connection.orm_default' => ConnectionFactory::class,
        ],
    ],
    'slm_queue' => [
        'worker_strategies' => [
            'default' => [
                MaxRunsStrategy::class => ['max_runs' => 1]
            ]
        ],
        'queues'            => [
            'my-doctrine-queue' => [
                'deleted_lifetime' => -1,
                'buried_lifetime'  => -1,
            ],
        ],
        'queue_manager'     => [
            'factories' => [
                'newsletter' => DoctrineQueueFactory::class
            ]
        ]
    ],
];
