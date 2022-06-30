<?php

return [
    'endpoint' => env('LARAVEL_CLOUDEVENTS_DAPR_ENDPOINT', 'http://localhost:3500'),
    'pubsub_name' => env('LARAVEL_CLOUDEVENTS_DAPR_PUBSUB_NAME'),
    'enabled' => env('LARAVEL_CLOUDEVENTS_ENABLED', true)
];
