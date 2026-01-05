<?php

namespace App\Queue;

use App\Queue\Jobs\KafkaJob;
use Enqueue\RdKafka\RdKafkaConnectionFactory;
use Enqueue\RdKafka\RdKafkaContext;
use Enqueue\RdKafka\RdKafkaMessage;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Queue\Queue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class KafkaQueue extends Queue implements QueueContract
{
    protected $brokers;
    protected $topic;
    protected $consumerGroup;
    protected $context;

    public function __construct(string $brokers, string $topic, string $consumerGroup, int $retryAfter = 90)
    {
        $this->brokers = $brokers;
        $this->topic = $topic;
        $this->consumerGroup = $consumerGroup;
        $this->retryAfter = $retryAfter;

        $factory = new RdKafkaConnectionFactory([
            'global' => [
                'group.id' => $consumerGroup,
                'metadata.broker.list' => $brokers,
                'enable.auto.commit' => 'false',
            ],
            'topic' => [
                'auto.offset.reset' => 'earliest',
            ],
        ]);

        $this->context = $factory->createContext();
    }

    public function size($queue = null)
    {
        // Kafka doesn't provide a direct way to get queue size
        return 0;
    }

    public function push($job, $data = '', $queue = null)
    {
        return $this->pushRaw($this->createPayload($job, $queue, $data), $queue);
    }

    public function pushRaw($payload, $queue = null, array $options = [])
    {
        $topicName = $queue ?? $this->topic;
        $topic = $this->context->createTopic($topicName);
        $message = $this->context->createMessage($payload);

        $producer = $this->context->createProducer();
        $producer->send($topic, $message);

        Log::info('Message sent to Kafka', [
            'topic' => $topicName,
            'payload_size' => strlen($payload),
        ]);

        return json_decode($payload, true)['uuid'] ?? null;
    }

    public function later($delay, $job, $data = '', $queue = null)
    {
        // Kafka doesn't support delayed messages natively
        // You can implement this using a separate topic or scheduler
        return $this->push($job, $data, $queue);
    }

    public function pop($queue = null)
    {
        $topicName = $queue ?? $this->topic;
        $topic = $this->context->createTopic($topicName);
        $consumer = $this->context->createConsumer($topic);

        try {
            if ($message = $consumer->receive(1000)) { // 1 second timeout
                Log::debug('Message received from Kafka', [
                    'topic' => $topicName,
                    'message_id' => $message->getMessageId(),
                ]);

                return new KafkaJob(
                    $this->container,
                    $this,
                    $message,
                    $topicName,
                    $this->retryAfter,
                    $consumer
                );
            }
        } catch (\Exception $e) {
            Log::error('Error receiving message from Kafka', [
                'topic' => $topicName,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    public function getConnectionName()
    {
        return 'kafka';
    }

    public function getContext()
    {
        return $this->context;
    }
}

