# Laravel Dapr CloudEvents
A Laravel library to publish Illuminate Events to dapr.

## Installation
Install the package:
```bash
composer require stafftastic/laravel-cloudevents
```

## Usage
1. Publish config file
```bash
php artisan vendor:publish --provider="stafftastic\LaravelCloudEvents\CloudEventServiceProvider"
```

2. Create your applications context:
```php
<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use stafftastic\CloudEvents\CloudEventable;
use stafftastic\CloudEvents\IsCloudEvent;

class UserCreated implements CloudEventable
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;
    use IsCloudEvent;

    /**
     * Create a new event instance.
     *
     * @param  \Domain\Users\Models\User  $user
     *
     * @return void
     */
    public function __construct(public User $user)
    {
    }

    public function getCloudEventType(): string
    {
        return 'com.stafftastic.users.created';
    }

    public function getCloudEventTopic(): string
    {
        return 'stafftastic';
    }

    public function toCloudEventData(): array
    {
        return [
            'user' => $this->user,
        ];
    }
}
```
