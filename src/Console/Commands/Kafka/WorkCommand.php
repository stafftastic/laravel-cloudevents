<?php

namespace stafftastic\CloudEvents\Console\Commands\Kafka;

use Illuminate\Console\Command;
use Junges\Kafka\Contracts\KafkaConsumerMessage;
use Junges\Kafka\Facades\Kafka;

class WorkCommand extends Command
{
    protected $signature = 'cloudevents:kafka:work 
            {--topics= : The topics to listen for messages (topic1,topic2,...,topicN)} 
            {--groupId= : The consumer group id} 
            {--commit=1}';
    protected $description = 'Consume cloudevents.';

    public function handle()
    {
        $topics = $this->option('topics') ?? config('cloudevents.kafka.work.topics');
        if (empty($topics)) {
            $this->error('Topics are required.');

            return;
        }

        /** @var \stafftastic\CloudEvents\Kafka\MessageHandler $handler */
        $handler = config('cloudevents.kafka.work.handler');
        if (!$handler) {
            $this->error('Handler is required.');

            return;
        }

        $consumer = Kafka::createConsumer($topics)
            ->withBrokers(config('cloudevents.kafka.brokers'))
            ->withConsumerGroupId($this->option('topics') ?? config('cloudevents.kafka.consumer_group_id'))
            ->withAutoCommit($this->option('commit'))
            ->withHandler(function (KafkaConsumerMessage $message) use ($handler) {
                (new $handler())->handle($message);
            })
            ->build();

        $consumer->consume();
    }
}
