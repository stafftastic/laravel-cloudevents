<?php

namespace stafftastic\CloudEvents;

use Illuminate\Http\Request;
use CloudEvents\V1\CloudEventInterface;
use CloudEvents\Serializers\JsonDeserializer;

class CloudEventBuilder
{
    public static function fromRequest(Request $request): CloudEventInterface
    {
        return JsonDeserializer::create()
            ->deserializeStructured($request->getContent());
    }
}
