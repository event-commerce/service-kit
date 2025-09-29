<?php

namespace EventSoft\ServiceKit\Managers;

use EventSoft\ServiceKit\Contracts\LogPublisherInterface;
use EventSoft\ServiceKit\Enums\LogLevel;
use EventSoft\ServiceKit\Enums\LogType;
use EventSoft\ServiceKit\Exceptions\ServiceKitException;

class LogManager
{
    public function __construct(
        private readonly LogPublisherInterface $publisher
    ) {
    }

    /** HTTP istek/yanıt logu */
    public function logHttp(array $data): void
    {
        $this->publish([
            'type' => LogType::HTTP->value,
            'level' => LogLevel::INFO->value,
            'timestamp' => now()->toIso8601String(),
            ...$data,
        ]);
    }

    /** İş (domain) olayı logu */
    public function logBusiness(array $data): void
    {
        $this->publish([
            'type' => LogType::BUSINESS->value,
            'level' => LogLevel::INFO->value,
            'timestamp' => now()->toIso8601String(),
            ...$data,
        ]);
    }

    /** Hata logu (yapılandırılmış payload) */
    public function logError(array $data): void
    {
        $this->publish([
            'type' => LogType::ERROR->value,
            'level' => LogLevel::ERROR->value,
            'timestamp' => now()->toIso8601String(),
            ...$data,
        ]);
    }

    /** Performans metriği logu */
    public function logPerformance(array $data): void
    {
        $this->publish([
            'type' => LogType::PERFORMANCE->value,
            'level' => LogLevel::INFO->value,
            'timestamp' => now()->toIso8601String(),
            ...$data,
        ]);
    }

    /** Güvenlik olayı logu */
    public function logSecurity(array $data): void
    {
        $this->publish([
            'type' => LogType::SECURITY->value,
            'level' => LogLevel::WARNING->value,
            'timestamp' => now()->toIso8601String(),
            ...$data,
        ]);
    }

    /** Denetim (audit) olayı logu */
    public function logAudit(array $data): void
    {
        $this->publish([
            'type' => LogType::AUDIT->value,
            'level' => LogLevel::INFO->value,
            'timestamp' => now()->toIso8601String(),
            ...$data,
        ]);
    }

    /** Sistem olayı logu */
    public function logSystem(array $data): void
    {
        $this->publish([
            'type' => LogType::SYSTEM->value,
            'level' => LogLevel::INFO->value,
            'timestamp' => now()->toIso8601String(),
            ...$data,
        ]);
    }

    /** Genel log metodu */
    public function log(LogType $type, LogLevel $level, array $data): void
    {
        $this->publish([
            'type' => $type->value,
            'level' => $level->value,
            'channel' => config('service-kit.logging.channel', 'service-kit'),
            'timestamp' => now()->toIso8601String(),
            ...$data,
        ]);
    }

    /** Bilgi seviyesinde basit mesaj */
    public function info(mixed $message, array $context = []): void
    {
        $this->log(LogType::SYSTEM, LogLevel::INFO, [
            'message' => $message,
            'context' => $context,
        ]);
    }

    /** Uyarı seviyesinde basit mesaj */
    public function warning(mixed $message, array $context = []): void
    {
        $this->log(LogType::SYSTEM, LogLevel::WARNING, [
            'message' => $message,
            'context' => $context,
        ]);
    }

    /** Hata seviyesinde basit mesaj */
    public function error(mixed $message, array $context = []): void
    {
        $this->log(LogType::ERROR, LogLevel::ERROR, [
            'message' => $message,
            'context' => $context,
        ]);
    }

    /** Debug seviyesinde basit mesaj */
    public function debug(mixed $message, array $context = []): void
    {
        $this->log(LogType::SYSTEM, LogLevel::DEBUG, [
            'message' => $message,
            'context' => $context,
        ]);
    }

    /** Acil seviyede basit mesaj */
    public function emergency(mixed $message, array $context = []): void
    {
        $this->log(LogType::SYSTEM, LogLevel::EMERGENCY, [
            'message' => $message,
            'context' => $context,
        ]);
    }

    /** Alarm seviyesinde basit mesaj */
    public function alert(mixed $message, array $context = []): void
    {
        $this->log(LogType::SYSTEM, LogLevel::ALERT, [
            'message' => $message,
            'context' => $context,
        ]);
    }

    /** Kritik seviyede basit mesaj */
    public function critical(mixed $message, array $context = []): void
    {
        $this->log(LogType::SYSTEM, LogLevel::CRITICAL, [
            'message' => $message,
            'context' => $context,
        ]);
    }

    /** Bildirim seviyesinde basit mesaj */
    public function notice(mixed $message, array $context = []): void
    {
        $this->log(LogType::SYSTEM, LogLevel::NOTICE, [
            'message' => $message,
            'context' => $context,
        ]);
    }

    /** Yayınlama */
    private function publish(array $data): void
    {
        if (!config('service-kit.logging.enabled', true)) {
            return;
        }

        // Envelope metadata (top-level): service & correlation
        $envelope = [
            'service' => config('service-kit.service.name', config('app.name', 'unknown-service')),
            'environment' => config('app.env', 'unknown'),
            'hostname' => gethostname(),
        ];
        $instanceId = config('service-kit.service.instance_id');
        if (!empty($instanceId)) {
            $envelope['instance_id'] = $instanceId;
        }
        if (!isset($data['correlation_id'])) {
            $correlationId = app(\EventSoft\ServiceKit\Correlation\CorrelationId::class);
            $envelope['correlation_id'] = $correlationId->get();
        }

        $payload = array_replace($envelope, $data);

        // DEBUG: Log'ları console'a yazdır
        error_log('=== SERVICE-KIT DEBUG ===');
        error_log('Original data: ' . json_encode($data, JSON_PRETTY_PRINT));
        error_log('Envelope: ' . json_encode($envelope, JSON_PRETTY_PRINT));
        error_log('Final payload: ' . json_encode($payload, JSON_PRETTY_PRINT));
        error_log('========================');

        try {
            $this->publisher->publish($payload);
        } catch (\Throwable $e) {
            if (config('app.debug', false)) {
                throw new ServiceKitException(
                    'Log publishing failed: ' . $e->getMessage(),
                    0,
                    $e,
                    ['log_data' => $payload]
                );
            }
        }
    }
}
