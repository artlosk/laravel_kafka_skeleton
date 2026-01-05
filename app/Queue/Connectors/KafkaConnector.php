<?php

namespace App\Queue\Connectors;

use App\Queue\KafkaQueue;
use Illuminate\Queue\Connectors\ConnectorInterface;
use Illuminate\Support\Arr;

class KafkaConnector implements ConnectorInterface
{
    public function connect(array $config)
    {
        return new KafkaQueue(
            $config['brokers'] ?? 'localhost:9092',
            $config['topic'] ?? 'default',
            $config['consumer_group'] ?? 'laravel-consumer',
            Arr::get($config, 'retry_after', 90)
        );
    }
}

