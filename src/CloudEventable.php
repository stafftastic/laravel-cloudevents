<?php

namespace stafftastic\CloudEvents;

use CloudEvents\V1\CloudEventInterface;

interface CloudEventable
{
    public function toCloudEvent(): CloudEventInterface;
    public function getCloudEventTopic(): string;
}
