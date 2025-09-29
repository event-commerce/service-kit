<?php

namespace EventSoft\ServiceKit\Context;

use Illuminate\Support\Facades\App;

class ContextManager
{
    private static ?array $context = null;

    public static function set(array $context): void
    {
        self::$context = array_merge(self::$context ?? [], $context);
    }

    public static function get(): array
    {
        return self::$context ?? [];
    }

    public static function getValue(string $key, mixed $default = null): mixed
    {
        return self::$context[$key] ?? $default;
    }

    public static function setTenant(string $tenantId): void
    {
        self::set([
            config('service-kit.context.tenant_id_key', 'tenant_id') => $tenantId,
        ]);
    }

    public static function setUser(string $userId): void
    {
        self::set([
            config('service-kit.context.user_id_key', 'user_id') => $userId,
        ]);
    }

    public static function setSession(string $sessionId): void
    {
        self::set([
            config('service-kit.context.session_id_key', 'session_id') => $sessionId,
        ]);
    }

    public static function setRequest(string $requestId): void
    {
        self::set([
            config('service-kit.context.request_id_key', 'request_id') => $requestId,
        ]);
    }

    public static function getTenantId(): ?string
    {
        return self::getValue(config('service-kit.context.tenant_id_key', 'tenant_id'));
    }

    public static function getUserId(): ?string
    {
        return self::getValue(config('service-kit.context.user_id_key', 'user_id'));
    }

    public static function getSessionId(): ?string
    {
        return self::getValue(config('service-kit.context.session_id_key', 'session_id'));
    }

    public static function getRequestId(): ?string
    {
        return self::getValue(config('service-kit.context.request_id_key', 'request_id'));
    }

    public static function clear(): void
    {
        self::$context = null;
    }

    public static function getEnrichedContext(): array
    {
        $context = self::get();

        if (App::has('correlation-id')) {
            $context['correlation_id'] = App::get('correlation-id');
        }

        $context['service'] = config('app.name', 'unknown');
        $context['environment'] = config('app.env', 'unknown');

        return $context;
    }
}
