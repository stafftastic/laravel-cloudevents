<?php

namespace stafftastic\CloudEvents;

use CloudEvents\V1\CloudEventInterface;

interface CloudEventable
{
    public function toCloudEvent(): CloudEventInterface;
    public function toCloudEventData(): array;
    public function getCloudEventType(): string;
    public function getCloudEventSource(): string;
    public function getCloudEventTopic(): string;
}
