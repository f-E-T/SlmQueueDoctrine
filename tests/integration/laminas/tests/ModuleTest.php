<?php

namespace SlmQueueDoctrineMvcTest;

use Laminas\Mvc\Application;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use SlmQueue\Queue\QueuePluginManager;
use SlmQueue\Strategy\StrategyPluginManager;
use SlmQueueDoctrine\Command\RecoverJobsCommand;
use SlmQueueDoctrine\Queue\DoctrineQueue;
use SlmQueueDoctrine\Strategy\ClearObjectManagerStrategy;
use SlmQueueDoctrine\Strategy\IdleNapStrategy;

class ModuleTest extends TestCase
{
    private ServiceManager $serviceManager;

    protected function setUp(): void
    {
        $application = Application::init(include __DIR__ . '/../config/application.config.php');
        $this->serviceManager = $application->getServiceManager();
    }

    public function testModuleRegistersRecoverJobsCommand(): void
    {
        static::assertInstanceOf(RecoverJobsCommand::class, $this->serviceManager->get(RecoverJobsCommand::class));
    }

    public function testModuleRegistersStrategies(): void
    {
        $strategyPluginManager = $this->serviceManager->get(StrategyPluginManager::class);

        static::assertInstanceOf(IdleNapStrategy::class, $strategyPluginManager->get(IdleNapStrategy::class));
        static::assertInstanceOf(
            ClearObjectManagerStrategy::class,
            $strategyPluginManager->get(ClearObjectManagerStrategy::class)
        );
    }

    public function testDoctrineQueueCanBeCreated(): void
    {
        $queue = $this->serviceManager->get(QueuePluginManager::class)->get('default');

        static::assertInstanceOf(DoctrineQueue::class, $queue);
    }
}
