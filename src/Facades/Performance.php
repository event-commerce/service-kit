<?php

namespace EventSoft\ServiceKit\Facades;

use EventSoft\ServiceKit\Managers\PerformanceManager;
use Illuminate\Support\Facades\Facade;

/**
 * Performance facade'i: operasyon bazlı süre ölçümü sağlar.
 *
 * start: İzlenecek operasyonu başlatır
 * stop: Operasyonu durdurup ms cinsinden süre döner ve loglar
 * getDuration: Durdurmadan anlık süreyi verir
 * isRunning: Operasyon izleniyor mu kontrol eder
 * getRunningOperations: İzlenmekte olan tüm operasyonları verir
 *
 * @method static void start(string $operation) İzlenecek operasyonu başlatır
 * @method static float|null stop(string $operation, array $context = []) Operasyonu durdurup ms döner
 * @method static float|null getDuration(string $operation) Anlık süreyi verir
 * @method static bool isRunning(string $operation) Operasyon izleniyor mu
 * @method static array getRunningOperations() Tüm operasyonları listeler
 */
class Performance extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return PerformanceManager::class;
    }
}

