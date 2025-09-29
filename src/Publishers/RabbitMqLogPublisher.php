<?php

namespace EventSoft\ServiceKit\Publishers;

use EventSoft\ServiceKit\Contracts\LogPublisherInterface;
use EventSoft\ServiceKit\Exceptions\LogPublishingException;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMqLogPublisher implements LogPublisherInterface
{
    /** @var array<string, mixed> */
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function publish(array $payload): void
    {
        try {
            $connection = $this->createConnection();
            $channel = $connection->channel();
            
            $this->declareExchange($channel);
            
            $message = $this->createMessage($payload);
            $channel->basic_publish(
                $message,
                $this->config['exchange'] ?? 'logs',
                $this->config['routing_key'] ?? 'log-messages'
            );
            
            $channel->close();
            $connection->close();
        } catch (\Throwable $e) {
            throw LogPublishingException::publishFailed($e->getMessage());
        }
    }

    public function isAvailable(): bool
    {
        try {
            $connection = $this->createConnection();
            $connection->close();
            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    private function createConnection(): AMQPStreamConnection
    {
        try {
            return new AMQPStreamConnection(
                $this->config['host'] ?? 'rabbitmq',
                (int) ($this->config['port'] ?? 5672),
                $this->config['user'] ?? 'guest',
                $this->config['password'] ?? 'guest',
                $this->config['vhost'] ?? '/'
            );
        } catch (\Throwable $e) {
            throw LogPublishingException::connectionFailed($e->getMessage());
        }
    }

    private function declareExchange($channel): void
    {
        $channel->exchange_declare(
            $this->config['exchange'] ?? 'logs',
            $this->config['exchange_type'] ?? 'topic',
            false,
            true,
            false
        );
    }

    private function createMessage(array $payload): AMQPMessage
    {
        return new AMQPMessage(
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
            [
                'content_type' => 'application/json',
                'delivery_mode' => ($this->config['persistent'] ?? true) ? 2 : 1,
            ]
        );
    }
}

