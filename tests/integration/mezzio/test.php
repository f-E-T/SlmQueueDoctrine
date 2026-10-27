<?php

require 'vendor/autoload.php';

@unlink('temp/database.sqlite');
@unlink('temp/succesful');

// Bootstrap application, and initialize an empty database.
$serviceManager = require 'config/container.php';
$connection = $serviceManager->get('doctrine.connection.orm_default');
$connection->executeStatement(file_get_contents(__DIR__ . '/../../queue_default.sqlite'));

// Populate with a job
$serviceManager->get(\SlmQueue\Queue\QueuePluginManager::class)->get('default')->push(new \App\TestJob());

// Run the queue for a single job
exec('vendor/bin/laminas slm-queue:start default');

// Assert that file was generated?
if (@file_get_contents('temp/succesful') !== 'YES') {
    echo 'Test was NOT successful.';
    exit(1);
}

// Recover jobs that run for too long
exec('vendor/bin/laminas slm-queue:doctrine:recover default', $output, $exitCode);

if ($exitCode !== 0) {
    echo 'Recovering jobs was NOT successful.';
    exit(1);
}

echo 'Test was successful.';
exit(0);
