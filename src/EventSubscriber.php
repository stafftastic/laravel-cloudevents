<?php

namespace stafftastic\CloudEvents;

class EventSubscriber
{
    public function __construct(
        protected EventPublisher $eventPublisher,
    ) {
    }

    public function handle(CloudEventable $event)
    {
        $this->eventPublisher->publish($event);
    }
}
