<?php

namespace EventSoft\ServiceKit\Contracts;

interface LogPublisherInterface
{
    /**
     * Publish structured log payload to central bus.
     *
     * @param array<string, mixed> $payload
     * @throws \EventSoft\ServiceKit\Exceptions\LogPublishingException
     */
    public function publish(array $payload): void;

    /**
     * Check if publisher is available.
     */
    public function isAvailable(): bool;
}

