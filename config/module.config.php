<?php

use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;
use Laminas\ServiceManager\Factory\InvokableFactory;
use SlmQueueDoctrine\Command\RecoverJobsCommand;
use SlmQueueDoctrine\Strategy\ClearObjectManagerStrategy;
use SlmQueueDoctrine\Strategy\IdleNapStrategy;

return [
    'dependencies' => [
        'factories' => [
            RecoverJobsCommand::class => ReflectionBasedAbstractFactory::class,
        ]
    ],
    'laminas-cli' => [
        'commands' => [
            'slm-queue:doctrine:recover' => RecoverJobsCommand::class,
        ],
    ],
    'slm_queue' => [
        /**
         * Worker Strategies
         */
        'worker_strategies' => [
            'default' => [
                IdleNapStrategy::class => ['nap_duration' => 1],
                ClearObjectManagerStrategy::class
            ],
            'queues'  => [
            ],
        ],
        /**
         * Strategy manager configuration
         */
        'strategy_manager'  => [
            'factories' => [
                IdleNapStrategy::class            => InvokableFactory::class,
                ClearObjectManagerStrategy::class => InvokableFactory::class,
            ],
        ],
    ],
];
