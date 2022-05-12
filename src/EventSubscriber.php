<?php

namespace stafftastic\CloudEvents;

class DomainEventSubscriber
{
    public function __construct(
        protected EventPublisher $eventPublisher,
    ) {
    }

    public function handle(string $eventName, array $data)
    {
        $this->eventPublisher->publish($data[0]);
    }
}
