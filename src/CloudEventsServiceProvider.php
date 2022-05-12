<?php

namespace stafftastic\CloudEvents;

use Dapr\Client\DaprClient;
use Dapr\Serialization\SerializationConfig;
use Dapr\Deserialization\DeserializationConfig;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

class CloudEventsServiceProvider extends ServiceProvider implements DeferrableProvider
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

        Event::listen(
            '*',
            [EventSubscriber::class, 'handle'],
        );
    }

    public function register()
    {
        $this->app->bind(
            DaprClient::class,
            fn (ContainerInterface $container) => DaprClient::clientBuilder()
                ->useHttpClient($this->app['config']['endpoint'])
                ->withLogger($container->get(LoggerInterface::class))
                ->withSerializationConfig($container->get(SerializationConfig::class))
                ->withDeserializationConfig($container->get(DeserializationConfig::class))
                ->build()
        );

        $this->app->bind(
            EventPublisher::class,
            fn ($app) => new Dapr\EventPublisher($app->make(DaprClient::class), $app['config']['cloudevents']),
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [EventPublisher::class, DaprClient::class];
    }
}
