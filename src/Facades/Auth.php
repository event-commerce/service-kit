<?php

namespace EventSoft\ServiceKit\Facades;

use EventSoft\ServiceKit\Services\AuthService;
use Illuminate\Support\Facades\Facade;
use Psr\Http\Message\ResponseInterface;

/**
 * Auth facade'i: auth-service'e HTTP istekleri gönderir.
 *
 * Chainable config:
 * @method static self retry(int $count) Deneme sayısını ayarlar
 * @method static self timeout(int $seconds) Zaman aşımını ayarlar
 * @method static self jitter(float $jitter) Jitter oranı ekler
 * @method static self withHeaders(array $headers) Ek header'lar ekler
 * @method static self withOptions(array $options) Guzzle opsiyonları ekler
 *
 * Requests:
 * @method static ResponseInterface get(string $path, array $options = []) GET isteği
 * @method static ResponseInterface post(string $path, array $options = []) POST isteği
 * @method static ResponseInterface put(string $path, array $options = []) PUT isteği
 * @method static ResponseInterface patch(string $path, array $options = []) PATCH isteği
 * @method static ResponseInterface delete(string $path, array $options = []) DELETE isteği
 */
class Auth extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return AuthService::class;
    }
}
