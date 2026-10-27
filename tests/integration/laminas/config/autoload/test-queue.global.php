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
    'service_manager' => [
        'factories' => [
            'doctrine.connection.orm_default' => \TestModule\ConnectionFactory::class,
        ],
    ],
];
