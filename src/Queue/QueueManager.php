<?php

namespace EventSoft\ServiceKit\Queue;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class QueueManager
{
    /** @var array<string, mixed> */
    private array $amqpConfig;

    /** @var array<string, string> */
    private array $channels;

    /** @var array<string, array<string, mixed>> */
    private array $settings;

    private int $deliveryMode;

    public function __construct()
    {
        $this->amqpConfig = (array) config('service-kit.rabbitmq', []);
        $this->channels = (array) config('service-kit.queues.channels', []);
        $this->settings = (array) config('service-kit.queues.settings', []);
        $this->deliveryMode = (int) config('service-kit.queues.delivery_mode', 2);
    }

    /**
     * Publish payload to a logical channel (logs|metrics|events)
     * @param array<string,mixed> $payload
     */
    public function publish(string $channelKey, array $payload): void
    {
        $queueName = $this->channels[$channelKey] ?? null;
        if (!$queueName) {
            return;
        }

        $connection = new AMQPStreamConnection(
            $this->amqpConfig['host'] ?? 'rabbitmq',
            (int) ($this->amqpConfig['port'] ?? 5672),
            $this->amqpConfig['user'] ?? 'guest',
            $this->amqpConfig['password'] ?? 'guest',
            $this->amqpConfig['vhost'] ?? '/'
        );
        $channel = $connection->channel();

        $durable = (bool) ($this->settings[$channelKey]['durable'] ?? true);
        $ttl = (int) ($this->settings[$channelKey]['ttl'] ?? 0);
        $maxLength = (int) ($this->settings[$channelKey]['max_length'] ?? 0);

        $args = [];
        if ($ttl > 0) { $args['x-message-ttl'] = ['I', $ttl]; }
        if ($maxLength > 0) { $args['x-max-length'] = ['I', $maxLength]; }

        // Declare queue
        $channel->queue_declare($queueName, false, $durable, false, false, false, $args);

        $message = new AMQPMessage(
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
            [
                'content_type' => 'application/json',
                'delivery_mode' => $this->deliveryMode,
            ]
        );

        $channel->basic_publish($message, '', $queueName);
        $channel->close();
        $connection->close();
    }

    /** @param string $channel @param array<string,mixed> $payload */
    public function send(string $channel, array $payload): void
    {
        // Service-kit envelope metadata'sını ekle
        $envelope = [
            'service' => config('service-kit.service.name', config('app.name', 'unknown-service')),
            'environment' => config('app.env', 'unknown'),
            'hostname' => gethostname(),
        ];
        $instanceId = config('service-kit.service.instance_id');
        if (!empty($instanceId)) {
            $envelope['instance_id'] = $instanceId;
        }
        if (!isset($payload['correlation_id'])) {
            $correlationId = app(\EventSoft\ServiceKit\Correlation\CorrelationId::class);
            $envelope['correlation_id'] = $correlationId->get();
        }
        
        $enrichedPayload = array_replace($envelope, $payload);
        $this->publish($channel, $enrichedPayload);
    }

    /** @param array<string,mixed> $payload */
    public function sendLog(array $payload): void
    {
        // Backward compatibility için log-messages channel'ına gönder
        $this->send('logs', $payload);
    }

    /** @param array<string,mixed> $payload */
    public function sendMetric(array $payload): void
    {
        $this->publish('metrics', $payload);
    }

    /** @param array<string,mixed> $payload */
    public function sendEvent(array $payload): void
    {
        $this->publish('events', $payload);
    }
}
