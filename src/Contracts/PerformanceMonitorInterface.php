<?php

namespace EventSoft\ServiceKit\Contracts;

interface PerformanceMonitorInterface
{
    /**
     * Start timing a named operation.
     */
    public function start(string $operation): void;

    /**
     * Stop timing and return duration in milliseconds.
     */
    public function stop(string $operation): ?float;

    /**
     * Get current duration without stopping.
     */
    public function getDuration(string $operation): ?float;

    /**
     * Check if operation is being monitored.
     */
    public function isRunning(string $operation): bool;

    /**
     * Get all running operations.
     *
     * @return array<string>
     */
    public function getRunningOperations(): array;
}

