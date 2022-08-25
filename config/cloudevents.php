<?php

return [
    'brokers' => env('LARAVEL_CLOUDEVENTS_KAFKA_BROKERS', 'kafka-broker:9092'),
    'enabled' => env('LARAVEL_CLOUDEVENTS_ENABLED', true),
    'debug_enabled' => env('LARAVEL_CLOUDEVENTS_DEBUG_ENABLED', false)
];
