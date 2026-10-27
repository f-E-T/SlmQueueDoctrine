<?php

return [
    'slm_queue' => [
        'queues' => [
            'default' => [],
        ],
        'queue_manager' => [
            'factories' => [
                'default' => \SlmQueueDoctrine\Factory\DoctrineQueueFactory::class,
            ],
        ],
        'worker_strategies' => [
            'default' => [
                \SlmQueue\Strategy\MaxRunsStrategy::class => ['max_runs' => 1],
                \SlmQueue\Strategy\WorkerLifetimeStrategy::class => ['lifetime' => 3],
            ],
        ],
    ],
    'dependencies' => [
        'factories' => [
            'doctrine.connection.orm_default' => \App\ConnectionFactory::class,
        ],
    ],
];
