<?php

namespace stafftastic\CloudEvents;

use Illuminate\Support\Str;
use CloudEvents\V1\CloudEventImmutable;

trait IsCloudEvent
{
    public function toCloudEvent(): CloudEventImmutable
    {
        return new CloudEventImmutable(
            id: Str::uuid(),
            source: $this->getCloudEventSource(),
            type: $this->getCloudEventType(),
            data: $this->toCloudEventData(),
            dataContentType: 'application/json',
        );
    }

    public function getCloudEventSource(): string
    {
        return config('cloudevents.default.source');
    }
}
