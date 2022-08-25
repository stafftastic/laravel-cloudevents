<?php

return [
    'brokers' => env('LARAVEL_CLOUDEVENTS_KAFKA_BROKERS', 'http://localhost:3500'),
    'enabled' => env('LARAVEL_CLOUDEVENTS_ENABLED', true),
    'debug_enabled' => env('LARAVEL_CLOUDEVENTS_DEBUG_ENABLED', false)
];
