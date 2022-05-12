<?php

namespace stafftastic\CloudEvents\Dapr;

use Dapr\Client\DaprClient;
use Dapr\PubSub\Topic;
use stafftastic\CloudEvents\CloudEventable;
use stafftastic\CloudEvents\EventPublisher as Publisher;

class EventPublisher implements Publisher
{
    public function __construct(
        protected DaprClient $daprClient,
        protected array $config,
    ) {
    }

    public function buildTopic(string $topic): Topic
    {
        return new Topic(
            $this->config['pubsub_name'],
            $topic,
            $this->daprClient,
        );
    }

    public function publish(CloudEventable $event): bool
    {
        return $this
            ->buildTopic($event->getCloudEventTopic())
            ->publish($event->toCloudEvent());
    }
}
