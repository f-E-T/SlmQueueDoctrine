SlmQueueDoctrine
================

[![Latest Stable Version](https://poser.pugx.org/fet/queue-doctrine/v/stable.png)](https://packagist.org/packages/fet/queue-doctrine)

This is a maintained fork of [Webador/SlmQueueDoctrine](https://github.com/Webador/SlmQueueDoctrine)
(`slm/queue-doctrine`), which is no longer maintained. It is published as `fet/queue-doctrine`, starting at version 5.0,
and supports the [PHP versions that are currently supported](https://www.php.net/supported-versions.php). The
`SlmQueueDoctrine\` namespace is unchanged, so `fet/queue-doctrine` cannot be installed next to `slm/queue-doctrine`.
See [UPGRADE.md](UPGRADE.md) when coming from `slm/queue-doctrine` 4.x.

SlmQueueDoctrine is the [Doctrine DBAL](https://www.doctrine-project.org/projects/dbal.html) adapter for
[fet/queue](https://github.com/f-E-T/SlmQueue) (SlmQueue). It stores jobs in a database table and works in Laminas
applications, such as Mezzio, that use [laminas-servicemanager](https://github.com/laminas/laminas-servicemanager).
Laminas MVC is still supported on PHP 8.2 to 8.4, but deprecated (see [Laminas MVC](#laminas-mvc)).

Laminas MVC
-----------

The `SlmQueueDoctrine` module works as before; no code changes are needed. Laminas MVC does not support PHP 8.5, so in
a Laminas MVC application SlmQueueDoctrine runs on PHP 8.2, 8.3 and 8.4 only.
[Laminas MVC is retiring](https://getlaminas.org/blog/2026-03-06-laminas-mvc-eol-schedule.html): it is security-only
until 31 December 2028. `SlmQueueDoctrine\Module` is therefore deprecated and will be removed in a future major
version after Laminas MVC's end of life. The Laminas project recommends moving to Mezzio.

Requirements
------------
* PHP 8.2, 8.3, 8.4 or 8.5
* [fet/queue](https://github.com/f-E-T/SlmQueue) 4.x
* [Doctrine DBAL](https://www.doctrine-project.org/projects/dbal.html) 3.10 or 4.x

Installation
------------

Run `composer require fet/queue-doctrine`.

If you have the [laminas/laminas-component-installer](https://github.com/laminas/laminas-component-installer) package
installed, it will ask you to enable the module (and `SlmQueue`), both in Laminas MVC and Mezzio. Otherwise:

* in Mezzio, add `SlmQueue\ConfigProvider` and `SlmQueueDoctrine\ConfigProvider` to your `config/config.php`:

```php
$aggregator = new ConfigAggregator([
    \SlmQueue\ConfigProvider::class,
    \SlmQueueDoctrine\ConfigProvider::class,
    // ...
]);
```

* in Laminas MVC (PHP 8.2 to 8.4 only), add `SlmQueue` and `SlmQueueDoctrine` to the modules in your
  `config/application.config.php` (or `config/modules.config.php`).

Documentation
-------------

Before reading SlmQueueDoctrine documentation, please read the [SlmQueue documentation](https://github.com/f-E-T/SlmQueue).

### Configuring the connection

SlmQueueDoctrine needs a `Doctrine\DBAL\Connection` from the service container. By default it uses the service
`doctrine.connection.orm_default`; see [Configuring queues](#configuring-queues) to use another one. If your application
already registers a Doctrine connection (for example with
[roave/psr-container-doctrine](https://github.com/Roave/psr-container-doctrine)), nothing else is needed. Otherwise,
register a factory for it (in Laminas MVC, under `service_manager` instead of `dependencies`):

```php
<?php

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

return [
    'dependencies' => [
        'factories' => [
            'doctrine.connection.orm_default' => static fn (): Connection => DriverManager::getConnection([
                'driver'   => 'pdo_mysql',
                'host'     => 'localhost',
                'port'     => 3306,
                'user'     => 'username',
                'password' => 'password',
                'dbname'   => 'database',
            ]),
        ],
    ],
];
```

### Creating the table from SQL file

You must create the required table that will contain the queue's you may use the schema located in 'data/queue_default.sql'. If you change the table name look at [Configuring queues](#configuring-queues)

```
>mysql database < data/queue_default.sql
```
### Creating the table from Doctrine Entity
There is an alternative way to create 'queue_default' table in your database by copying Doctrine Entity 'data/DefaultQueue.php' to your entity folder ('Application\Entity' in our example) and executing Doctrine's 'orm:schema-tool:update' command which should create the table for you. Notice that DefaultQueue entity is only used for table creation and is not used by this module internally.


### Adding queues

```php
return [
  'slm_queue' => [
    'queue_manager' => [
      'factories' => [
        'foo' => 'SlmQueueDoctrine\Factory\DoctrineQueueFactory'
      ]
    ]
  ]
];
```
### Adding jobs

```php
return [
  'slm_queue' => [
    'job_manager' => [
      'factories' => [
        'My\Job' => 'My\JobFactory'
      ]
    ]
  ]
];

``` 
### Configuring queues

The following options can be set per queue ;
	
- connection (defaults to 'doctrine.connection.orm_default') : Name of the registered doctrine connection service
- table_name (defaults to 'queue_default') : Table name which should be used to store jobs
- deleted_lifetime (defaults to 0) : How long to keep deleted (successful) jobs (in minutes)
- buried_lifetime (defaults to 0) : How long to keep buried (failed) jobs (in minutes)


```php
return [
  'slm_queue' => [
    'queues' => [
      'foo' => [
        // ...
      ]
    ]
  ]
];
 ```
 
Provided Worker Strategies
--------------------------

In addition to the provided strategies by [SlmQueue](https://github.com/f-E-T/SlmQueue/blob/master/docs/6.Events.md) SlmQueueDoctrine comes with these strategies;

#### ClearObjectManagerStrategy

This strategy will clear the ObjectManager before execution of individual jobs. The job must implement
[SlmQueueDoctrine\Persistence\ObjectManagerAwareInterface](src/Persistence/ObjectManagerAwareInterface.php).

listens to:

- `process.job` event at priority 1000

options:

- none

This strategy is enabled by default.

#### IdleNapStrategy

When no jobs are available in the queue this strategy will make the worker wait for a specific amount time before quering the database again.

listens to:

- `process.idle` event at priority 1

options:

- `nap_duration` defaults to 1 (second)

This strategy is enabled by default.

### Operations on queues

#### push

Valid options are:

* scheduled: the time when the job will be scheduled to run next
	* numeric string or integer - interpreted as a timestamp
	* string parserable by the DateTime object
	* DateTime instance
* delay: the delay before a job become available to be popped (defaults to 0 - no delay -)
	* numeric string or integer - interpreted as seconds
	* string parserable (ISO 8601 duration) by DateTimeInterval::__construct
	* string parserable (relative parts) by DateTimeInterval::createFromDateString
	* DateTimeInterval instance
* priority: the lower the priority is, the sooner the job get popped from the queue (default to 1024)

Examples:
```php
// scheduled for execution asap
$queue->push($job);

// will get executed before jobs that have higher priority
$queue->push($job, [
    'priority' => 200,
]);

// scheduled for execution 2015-01-01 00:00:00 (system timezone applies)
$queue->push($job, [
    'scheduled' => 1420070400,
]);

// scheduled for execution 2015-01-01 00:00:00 (system timezone applies)
$queue->push($job, [
    'scheduled' => '2015-01-01 00:00:00'
]);

// scheduled for execution at 2015-01-01 01:00:00
$queue->push($job, [
    'scheduled' => '2015-01-01 00:00:00',
    'delay' => 3600
]);

// scheduled for execution at now + 300 seconds
$queue->push($job, [
    'delay' => 'PT300S'
]);

// scheduled for execution at now + 2 weeks (1209600 seconds)
$queue->push($job, [
    'delay' => '2 weeks'
]);

// scheduled for execution at now + 300 seconds
$queue->push($job, [
    'delay' => new DateInterval("PT300S")
]);
```


### Worker actions

Interact with workers from the command line from within the root folder of your application

#### Starting a worker
Start a worker that will keep monitoring a specific queue for jobs scheduled to be processed. This worker will continue until it has reached certain criteria (exceeds a memory limit or has processed a specified number of jobs).

`vendor/bin/laminas slm-queue:start <queueName>`

A worker will exit when you press cntr-C *after* it has finished the current job it is working on. (PHP doesn't support signal handling on Windows)

#### Recovering jobs

To recover jobs which are in the 'running' state for prolonged period of time (specified in minutes) use the following command.

`vendor/bin/laminas slm-queue:doctrine:recover <queueName> [--executionTime=]`

*Note : Workers that are processing a job that is being recovered are NOT stopped.*

