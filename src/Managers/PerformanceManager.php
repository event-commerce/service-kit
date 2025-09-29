<?php

namespace EventSoft\ServiceKit\Managers;

use EventSoft\ServiceKit\Contracts\PerformanceMonitorInterface;
use EventSoft\ServiceKit\Enums\LogLevel;
use EventSoft\ServiceKit\Enums\LogType;

class PerformanceManager
{
    public function __construct(
        private readonly PerformanceMonitorInterface $monitor
    ) {
    }

    /**
     * Start monitoring an operation.
     */
    public function start(string $operation): void
    {
        if (!config('service-kit.performance.enabled', false)) {
            return;
        }

        $this->monitor->start($operation);
    }

    /**
     * Stop monitoring and log performance data.
     *
     * @param array<string, mixed> $context
     */
    public function stop(string $operation, array $context = []): ?float
    {
        if (!config('service-kit.performance.enabled', false)) {
            return null;
        }

        $duration = $this->monitor->stop($operation);
        
        if ($duration !== null) {
            $this->logPerformance($operation, $duration, $context);
        }

        return $duration;
    }

    /**
     * Get current duration without stopping.
     */
    public function getDuration(string $operation): ?float
    {
        return $this->monitor->getDuration($operation);
    }

    /**
     * Check if operation is being monitored.
     */
    public function isRunning(string $operation): bool
    {
        return $this->monitor->isRunning($operation);
    }

    /**
     * Get all running operations.
     *
     * @return array<string>
     */
    public function getRunningOperations(): array
    {
        return $this->monitor->getRunningOperations();
    }

    /**
     * Log performance data.
     *
     * @param array<string, mixed> $context
     */
    private function logPerformance(string $operation, float $duration, array $context): void
    {
        $logManager = app(LogManager::class);
        
        $logManager->log(LogType::PERFORMANCE, LogLevel::INFO, [
            'operation' => $operation,
            'duration_ms' => $duration,
            'context' => $context,
        ]);
    }
}

