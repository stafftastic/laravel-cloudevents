<?php

namespace stafftastic\CloudEvents\Console\Commands\Kafka;

use CloudEvents\Serializers\JsonDeserializer;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Junges\Kafka\Contracts\CanConsumeMessages;
use Junges\Kafka\Contracts\KafkaConsumerMessage;
use Junges\Kafka\Facades\Kafka;
use Throwable;
use CloudEvents\V1\CloudEventInterface;

class WorkCommand extends Command
{
    protected $signature = 'cloudevents:kafka:work 
            {--topics= : The topics to listen for messages (topic1,topic2,...,topicN)} 
            {--handler= : The handler to process messages}
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
        $handler = $this->option('handler') ?? config('cloudevents.kafka.work.handler');
        if (! $handler) {
            $this->error('Handler is required.');

            return;
        }

        $consumer = Kafka::createConsumer(
            $topics,
            $this->option('topics') ?? config('cloudevents.kafka.consumer_group_id'),
            config('cloudevents.kafka.brokers')
        )
            ->withAutoCommit($this->option('commit'))
            ->withHandler(function (KafkaConsumerMessage $message) use ($handler) {
                try {
                    $this->writeOutput($message, 'starting');
                    (new $handler())->handle($message);
                    $this->writeOutput($message, 'success');
                } catch (Throwable $throwable) {
                    $this->writeOutput($message, 'failed');
                    throw $throwable;
                }
            })
            ->build();

        if ($this->supportsAsyncSignals()) {
            pcntl_signal(SIGINT, fn() => $this->gracefulShutdown($consumer));
        }

        $consumer->consume();
    }

    protected function writeOutput(KafkaConsumerMessage $message, string $status): void
    {
        switch ($status) {
            case 'starting':
                $this->writeStatus($message, 'Processing', 'comment');
                break;
            case 'success':
                $this->writeStatus($message, 'Processed', 'info');
                break;
            case 'failed':
                $this->writeStatus($message, 'Failed', 'error');
                break;
        }
    }

    protected function writeStatus(KafkaConsumerMessage $message, string $status, string $type): void
    {
        /** @var CloudEventInterface $cloudevent */
        $cloudevent = JsonDeserializer::create()->deserializeStructured($message->getBody());
        $this->output->writeln(sprintf(
            "<{$type}>[%s][%s] %s</{$type}> topic: %s offset: %s type: %s",
            Carbon::now()->format('Y-m-d H:i:s'),
            $cloudevent->getId(),
            str_pad("{$status}:", 11),
            $message->getTopicName(),
            $message->getOffset(),
            $cloudevent->getType()
        ));
    }

    protected function supportsAsyncSignals(): bool
    {
        return extension_loaded('pcntl');
    }

    protected function gracefulShutdown(CanConsumeMessages $consumer): void
    {
        $consumer->stopConsume(function () {
            $this->line('Stopped consuming.');
            $this->kill();
        });
    }

    protected function kill($status = 0){
        if (extension_loaded('posix')) {
            posix_kill(getmypid(), SIGKILL);
        }

        exit($status);
    }
}
