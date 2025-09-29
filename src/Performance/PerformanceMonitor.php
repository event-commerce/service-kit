<?php

namespace EventSoft\ServiceKit\Performance;

use EventSoft\ServiceKit\Contracts\PerformanceMonitorInterface;

class PerformanceMonitor implements PerformanceMonitorInterface
{
    /** @var array<string, float> */
    private array $startTimes = [];

    public function start(string $operation): void
    {
        $this->startTimes[$operation] = microtime(true);
    }

    public function stop(string $operation): ?float
    {
        if (!isset($this->startTimes[$operation])) {
            return null;
        }

        $duration = (microtime(true) - $this->startTimes[$operation]) * 1000;
        unset($this->startTimes[$operation]);

        return $duration;
    }

    public function getDuration(string $operation): ?float
    {
        if (!isset($this->startTimes[$operation])) {
            return null;
        }

        return (microtime(true) - $this->startTimes[$operation]) * 1000;
    }

    public function isRunning(string $operation): bool
    {
        return isset($this->startTimes[$operation]);
    }

    public function getRunningOperations(): array
    {
        return array_keys($this->startTimes);
    }
}
