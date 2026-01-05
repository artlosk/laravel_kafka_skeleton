<?php

namespace App\Queue\Jobs;

use App\Queue\KafkaQueue;
use Enqueue\RdKafka\RdKafkaConsumer;
use Enqueue\RdKafka\RdKafkaMessage;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\Job as JobContract;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Support\Str;

class KafkaJob extends Job implements JobContract
{
    protected $kafkaMessage;
    protected $topic;
    protected $kafkaQueue;
    protected $consumer;

    public function __construct(
        Container $container,
        KafkaQueue $kafkaQueue,
        RdKafkaMessage $kafkaMessage,
        string $topic,
        int $retryAfter = 90,
        ?RdKafkaConsumer $consumer = null
    ) {
        $this->container = $container;
        $this->kafkaQueue = $kafkaQueue;
        $this->kafkaMessage = $kafkaMessage;
        $this->topic = $topic;
        $this->retryAfter = $retryAfter;
        $this->consumer = $consumer;
        $this->connectionName = 'kafka';
    }

    public function getJobId()
    {
        return $this->kafkaMessage->getMessageId() ?? Str::uuid()->toString();
    }

    public function getRawBody()
    {
        return $this->kafkaMessage->getBody();
    }

    public function attempts()
    {
        $payload = json_decode($this->getRawBody(), true);
        return ($payload['attempts'] ?? 0) + 1;
    }

    public function delete()
    {
        parent::delete();
        
        // Acknowledge the message in Kafka
        if ($this->consumer) {
            $this->consumer->acknowledge($this->kafkaMessage);
        } else {
            // Fallback: create a new consumer if not provided
            $topic = $this->kafkaQueue->getContext()->createTopic($this->topic);
            $consumer = $this->kafkaQueue->getContext()->createConsumer($topic);
            $consumer->acknowledge($this->kafkaMessage);
        }
    }

    public function release($delay = 0)
    {
        parent::release($delay);
        
        // Reject the message and requeue it
        if ($this->consumer) {
            $this->consumer->reject($this->kafkaMessage, true); // true = requeue
        } else {
            // Fallback: create a new consumer if not provided
            $topic = $this->kafkaQueue->getContext()->createTopic($this->topic);
            $consumer = $this->kafkaQueue->getContext()->createConsumer($topic);
            $consumer->reject($this->kafkaMessage, true); // true = requeue
        }
    }

    public function failed($e)
    {
        $this->markAsFailed();
        
        $payload = json_decode($this->getRawBody(), true);
        $class = $payload['displayName'] ?? $payload['job'] ?? null;
        
        if ($class && class_exists($class)) {
            $job = unserialize($payload['data']['command']);
            if (method_exists($job, 'failed')) {
                $job->failed($e);
            }
        }
    }

    public function getQueue()
    {
        return $this->topic;
    }
}

