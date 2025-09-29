<?php

namespace EventSoft\ServiceKit\Factories;

use EventSoft\ServiceKit\Enums\LogLevel;
use EventSoft\ServiceKit\Enums\LogType;

class LogEntryFactory
{
    /**
     * Create HTTP log entry.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public static function createHttp(array $data): array
    {
        return [
            'type' => LogType::HTTP->value,
            'level' => LogLevel::INFO->value,
            'timestamp' => now()->toIso8601String(),
            ...$data,
        ];
    }

    /**
     * Create business event log entry.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public static function createBusiness(array $data): array
    {
        return [
            'type' => LogType::BUSINESS->value,
            'level' => LogLevel::INFO->value,
            'timestamp' => now()->toIso8601String(),
            ...$data,
        ];
    }

    /**
     * Create error log entry.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public static function createError(array $data): array
    {
        return [
            'type' => LogType::ERROR->value,
            'level' => LogLevel::ERROR->value,
            'timestamp' => now()->toIso8601String(),
            ...$data,
        ];
    }

    /**
     * Create performance log entry.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public static function createPerformance(array $data): array
    {
        return [
            'type' => LogType::PERFORMANCE->value,
            'level' => LogLevel::INFO->value,
            'timestamp' => now()->toIso8601String(),
            ...$data,
        ];
    }

    /**
     * Create security log entry.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public static function createSecurity(array $data): array
    {
        return [
            'type' => LogType::SECURITY->value,
            'level' => LogLevel::WARNING->value,
            'timestamp' => now()->toIso8601String(),
            ...$data,
        ];
    }

    /**
     * Create audit log entry.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public static function createAudit(array $data): array
    {
        return [
            'type' => LogType::AUDIT->value,
            'level' => LogLevel::INFO->value,
            'timestamp' => now()->toIso8601String(),
            ...$data,
        ];
    }

    /**
     * Create system log entry.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public static function createSystem(array $data): array
    {
        return [
            'type' => LogType::SYSTEM->value,
            'level' => LogLevel::INFO->value,
            'timestamp' => now()->toIso8601String(),
            ...$data,
        ];
    }

    /**
     * Create custom log entry.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public static function create(LogType $type, LogLevel $level, array $data): array
    {
        return [
            'type' => $type->value,
            'level' => $level->value,
            'timestamp' => now()->toIso8601String(),
            ...$data,
        ];
    }
}

