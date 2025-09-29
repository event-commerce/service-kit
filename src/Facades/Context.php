<?php

namespace EventSoft\ServiceKit\Facades;

use EventSoft\ServiceKit\Context\ContextManager;
use Illuminate\Support\Facades\Facade;

/**
 * Context facade'i: tenant/user/session/request/correlation context yönetimi sağlar.
 *
 * set: Mevcut context'e toplu değer ekler/birleştirir
 * get: Tüm context'i döner
 * getValue: Anahtar bazlı context değeri döner
 * setTenant: Tenant kimliğini ayarlar
 * setUser: Kullanıcı kimliğini ayarlar
 * setSession: Session kimliğini ayarlar
 * setRequest: Request kimliğini ayarlar
 * getTenantId: Tenant kimliğini döner
 * getUserId: Kullanıcı kimliğini döner
 * getSessionId: Session kimliğini döner
 * getRequestId: Request kimliğini döner
 * clear: Context'i sıfırlar
 * getEnrichedContext: Servis/çevre/correlation ile zenginleştirilmiş context döner
 *
 * @method static void set(array $context) Context ekler
 * @method static array get() Tüm context'i döner
 * @method static mixed getValue(string $key, mixed $default = null) Anahtar bazlı değer döner
 * @method static void setTenant(string $tenantId) Tenant kimliğini ayarlar
 * @method static void setUser(string $userId) Kullanıcı kimliğini ayarlar
 * @method static void setSession(string $sessionId) Session kimliğini ayarlar
 * @method static void setRequest(string $requestId) Request kimliğini ayarlar
 * @method static string|null getTenantId() Tenant kimliğini döner
 * @method static string|null getUserId() Kullanıcı kimliğini döner
 * @method static string|null getSessionId() Session kimliğini döner
 * @method static string|null getRequestId() Request kimliğini döner
 * @method static void clear() Context'i sıfırlar
 * @method static array getEnrichedContext() Zenginleştirilmiş context'i döner
 */
class Context extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ContextManager::class;
    }
}

