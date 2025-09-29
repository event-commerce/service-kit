<?php

namespace EventSoft\ServiceKit\Facades;

use EventSoft\ServiceKit\Queue\QueueManager;
use Illuminate\Support\Facades\Facade;

/**
 * Queue facade'i: log/metrics/events kanallar 1na publish.
 *
 * @method static void publish(string $channelKey, array $payload) Kanal 1 key ile publish
 * @method static void sendLog(array $payload) Log kanal 1
 * @method static void sendMetric(array $payload) Metric kanal 1
 * @method static void sendEvent(array $payload) Event kanal 1
 */
class Queue extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return QueueManager::class;
    }
}
