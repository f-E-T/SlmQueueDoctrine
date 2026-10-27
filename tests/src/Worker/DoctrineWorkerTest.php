<?php

namespace SlmQueueDoctrineTest\Worker;

use Laminas\EventManager\EventManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SlmQueue\Strategy\MaxRunsStrategy;
use SlmQueueDoctrine\Queue\DoctrineQueueInterface;
use SlmQueueDoctrine\Worker\DoctrineWorker;
use SlmQueueDoctrineTest\Asset;

class DoctrineWorkerTest extends TestCase
{
    /**
     * @var DoctrineQueueInterface&MockObject
     */
    protected $queue;

    /**
     * @var DoctrineWorker
     */
    protected $worker;

    public function setUp(): void
    {
        $this->worker  = new DoctrineWorker(new EventManager());
        $this->queue   = $this->createMock(DoctrineQueueInterface::class);

        // set max runs so our tests won't run forever
        $maxRuns = new MaxRunsStrategy();
        $maxRuns->setMaxRuns(1);

        $maxRuns->attach($this->worker->getEventManager());
    }

    public function testAssertJobIsDeletedIfNoExceptionIsThrown(): void
    {
        $job = new Asset\SimpleJob();

        $this->queue->expects($this->once())
            ->method('delete')
            ->willReturnCallback(function () use ($job) {
                $job->setContent('deleted');
            });

        $this->worker->processJob($job, $this->queue);

        static::assertEquals('deleted', $job->getContent());
    }

    public function testAssertJobIsReleasedIfReleasableExceptionIsThrown(): void
    {
        $job = new Asset\ReleasableJob();

        $this->queue->expects($this->once())
            ->method('release')
            ->willReturnCallback(function () use ($job) {
                $job->setContent('released');
            });

        $this->worker->processJob($job, $this->queue);

        static::assertEquals('released', $job->getContent());
    }

    public function testAssertJobIsBuriedIfBuryableExceptionIsThrown(): void
    {
        $job = new Asset\BuryableJob();

        $this->queue->expects($this->once())
            ->method('bury')
            ->willReturnCallback(function () use ($job) {
                $job->setContent('buried');
            });

        $this->worker->processJob($job, $this->queue);

        static::assertEquals('buried', $job->getContent());
    }

    public function testAssertJobIsBuriedIfAnyExceptionIsThrown(): void
    {
        $job = new Asset\ExceptionJob();

        $this->queue->expects($this->once())
            ->method('bury')
            ->willReturnCallback(function () use ($job) {
                $job->setContent('buried');
            });

        $this->worker->processJob($job, $this->queue);

        static::assertEquals('buried', $job->getContent());
    }

    public function testAssertJobIsBuriedIfAnythingIsThrown(): void
    {
        $job = new Asset\ThrowableJob();

        $this->queue->expects($this->once())
            ->method('bury')
            ->willReturnCallback(function () use ($job) {
                $job->setContent('buried');
            });

        $this->worker->processJob($job, $this->queue);

        static::assertEquals('buried', $job->getContent());
    }
}
