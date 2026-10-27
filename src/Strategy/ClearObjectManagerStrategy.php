<?php

namespace SlmQueueDoctrine\Strategy;

use Laminas\EventManager\EventManagerInterface;
use SlmQueue\Strategy\AbstractStrategy;
use SlmQueue\Worker\Event\ProcessJobEvent;
use SlmQueue\Worker\Event\WorkerEventInterface;
use SlmQueueDoctrine\Persistence\ObjectManagerAwareInterface;

class ClearObjectManagerStrategy extends AbstractStrategy
{
    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(
            WorkerEventInterface::EVENT_PROCESS_JOB,
            [$this, 'onClear'],
            1000
        );
    }

    public function onClear(ProcessJobEvent $event): void
    {
        $job = $event->getJob();

        if (! $job instanceof ObjectManagerAwareInterface) {
            return;
        }

        $job->getObjectManager()->clear();
    }
}
