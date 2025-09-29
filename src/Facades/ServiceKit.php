<?php

namespace EventSoft\ServiceKit\Facades;

use EventSoft\ServiceKit\Enums\LogLevel;
use EventSoft\ServiceKit\Enums\LogType;
use EventSoft\ServiceKit\Managers\LogManager;
use EventSoft\ServiceKit\Managers\PerformanceManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void logHttp(array $data)
 * @method static void logBusiness(array $data)
 * @method static void logError(array $data)
 * @method static void logPerformance(array $data)
 * @method static void logSecurity(array $data)
 * @method static void logAudit(array $data)
 * @method static void logSystem(array $data)
 * @method static void log(LogType $type, LogLevel $level, array $data)
 * @method static void start(string $operation)
 * @method static float|null stop(string $operation, array $context = [])
 * @method static float|null getDuration(string $operation)
 * @method static bool isRunning(string $operation)
 * @method static array getRunningOperations()
 */
class ServiceKit extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'service-kit';
    }

    /**
     * Get LogManager instance.
     */
    public static function log(): LogManager
    {
        return app(LogManager::class);
    }

    /**
     * Get PerformanceManager instance.
     */
    public static function performance(): PerformanceManager
    {
        return app(PerformanceManager::class);
    }
}

