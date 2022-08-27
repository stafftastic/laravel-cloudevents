<?php

namespace stafftastic\CloudEvents\Kafka;

use CloudEvents\Serializers\Normalizers\V1\Normalizer;
use Junges\Kafka\Contracts\CanProduceMessages;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;
use stafftastic\CloudEvents\CloudEventable;
use stafftastic\CloudEvents\EventPublisher as Publisher;

class EventPublisher implements Publisher
{
    public function __construct(
        protected array $config,
    ) {
    }

    public function buildTopic(string $topic): CanProduceMessages
    {
        return Kafka::publishOn($topic);
    }

    public function publish(CloudEventable $event): bool
    {
        return $this
            ->buildTopic($event->getCloudEventTopic())
            ->withMessage($this->kafkaMessageForCloudEvent($event))
            ->send();
    }

    protected function kafkaMessageForCloudEvent(CloudEventable $event): Message
    {
        return new Message(
            headers: [
                'Content-Type' => 'application/cloudevents+json',
            ],
            body: (new Normalizer())->normalize($event->toCloudEvent(), false),
        );
    }
}
