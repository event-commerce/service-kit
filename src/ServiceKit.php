<?php

namespace EventSoft\ServiceKit;

use EventSoft\ServiceKit\Enums\LogLevel;
use EventSoft\ServiceKit\Enums\LogType;
use EventSoft\ServiceKit\Managers\LogManager;
use EventSoft\ServiceKit\Managers\PerformanceManager;

class ServiceKit
{
    public function __construct(
        private readonly LogManager $logManager,
        private readonly PerformanceManager $performanceManager
    ) {
    }

    /**
     * Log HTTP request/response data.
     *
     * @param array<string, mixed> $data
     */
    public function logHttp(array $data): void
    {
        $this->logManager->logHttp($data);
    }

    /**
     * Log business events.
     *
     * @param array<string, mixed> $data
     */
    public function logBusiness(array $data): void
    {
        $this->logManager->logBusiness($data);
    }

    /**
     * Log errors.
     *
     * @param array<string, mixed> $data
     */
    public function logError(array $data): void
    {
        $this->logManager->logError($data);
    }

    /**
     * Log performance metrics.
     *
     * @param array<string, mixed> $data
     */
    public function logPerformance(array $data): void
    {
        $this->logManager->logPerformance($data);
    }

    /**
     * Log security events.
     *
     * @param array<string, mixed> $data
     */
    public function logSecurity(array $data): void
    {
        $this->logManager->logSecurity($data);
    }

    /**
     * Log audit events.
     *
     * @param array<string, mixed> $data
     */
    public function logAudit(array $data): void
    {
        $this->logManager->logAudit($data);
    }

    /**
     * Log system events.
     *
     * @param array<string, mixed> $data
     */
    public function logSystem(array $data): void
    {
        $this->logManager->logSystem($data);
    }

    /**
     * Generic log method.
     *
     * @param array<string, mixed> $data
     */
    public function log(LogType $type, LogLevel $level, array $data): void
    {
        $this->logManager->log($type, $level, $data);
    }

    /**
     * Start monitoring an operation.
     */
    public function start(string $operation): void
    {
        $this->performanceManager->start($operation);
    }

    /**
     * Stop monitoring and log performance data.
     *
     * @param array<string, mixed> $context
     */
    public function stop(string $operation, array $context = []): ?float
    {
        return $this->performanceManager->stop($operation, $context);
    }

    /**
     * Get current duration without stopping.
     */
    public function getDuration(string $operation): ?float
    {
        return $this->performanceManager->getDuration($operation);
    }

    /**
     * Check if operation is being monitored.
     */
    public function isRunning(string $operation): bool
    {
        return $this->performanceManager->isRunning($operation);
    }

    /**
     * Get all running operations.
     *
     * @return array<string>
     */
    public function getRunningOperations(): array
    {
        return $this->performanceManager->getRunningOperations();
    }
}

