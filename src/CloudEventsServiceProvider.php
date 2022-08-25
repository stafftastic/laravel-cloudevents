<?php

namespace stafftastic\CloudEvents;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class CloudEventsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap unleash application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/cloudevents.php' => config_path('cloudevents.php'),
        ]);

        $this->mergeConfigFrom(
            __DIR__.'/../config/cloudevents.php',
            'cloudevents'
        );

        if ($this->app['config']['cloudevents']['enabled']) {
            Event::listen(
                CloudEventable::class,
                [EventSubscriber::class, 'handle'],
            );
        }
    }

    public function register()
    {
        if ($this->app['config']['cloudevents']['enabled']) {
            Config::set('kafka', $this->app['config']['cloudevents']['kafka']);
            $this->app->bind(
                EventPublisher::class,
                fn($app) => new Kafka\EventPublisher($app['config']['cloudevents']),
            );
        }
    }
}
