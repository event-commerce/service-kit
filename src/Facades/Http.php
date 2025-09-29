<?php

namespace EventSoft\ServiceKit\Facades;

use EventSoft\ServiceKit\Http\Client\HttpClient;
use Illuminate\Support\Facades\Facade;
use Psr\Http\Message\ResponseInterface;

/**
 * Http facade'i: retry/timeout/jitter destekli HTTP istekleri sağlar.
 *
 * retry: Toplam deneme sayısını ayarlar
 * timeout: İstek zaman aşımı (saniye) ayarlar
 * jitter: Retry gecikmesine jitter oranı ekler (0..1)
 * get/post/put/patch/delete: HTTP metoduna göre istek gönderir
 * request: Özel HTTP metodu ile istek gönderir
 *
 * @method static self retry(int $count) Deneme sayısını ayarlar
 * @method static self timeout(int $seconds) Zaman aşımını ayarlar
 * @method static self jitter(float $jitter) Jitter oranı ekler
 * @method static ResponseInterface get(string $url, array $options = []) GET isteği
 * @method static ResponseInterface post(string $url, array $options = []) POST isteği
 * @method static ResponseInterface put(string $url, array $options = []) PUT isteği
 * @method static ResponseInterface patch(string $url, array $options = []) PATCH isteği
 * @method static ResponseInterface delete(string $url, array $options = []) DELETE isteği
 * @method static ResponseInterface request(string $method, string $url, array $options = []) Özel istek
 */
class Http extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return HttpClient::class;
    }
}

