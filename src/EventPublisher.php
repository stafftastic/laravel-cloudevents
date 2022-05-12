<?php

namespace stafftastic\CloudEvents;

interface EventPublisher
{
    public function publish(CloudEventable $event): bool;
}
