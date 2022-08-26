<?php

namespace stafftastic\CloudEvents\Console\Commands\Kafka;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\InteractsWithTime;

class RestartWorkCommand extends Command
{
    use InteractsWithTime;

    protected $signature = 'cloudevents:kafka:restart';
    protected $description = 'Restart kafka consumers.';

    public function handle()
    {
        Cache::forever('laravel-kafka:consumer:restart', $this->currentTime());
        $this->info('Kafka consumers restart signal sent.');
    }
}
