<?php

namespace stafftastic\CloudEvents\Kafka;

use CloudEvents\CloudEventInterface;
use Junges\Kafka\Contracts\KafkaConsumerMessage;
use Throwable;

abstract class MessageHandler
{
    abstract public function handle(KafkaConsumerMessage $message, CloudEventInterface $cloudevent): void;

    public function failed(string $message, string $topic, Throwable $exception): void
    {
        throw $exception;
    }

    public function producerKey(string $message): ?string
    {
        return null;
    }
}
