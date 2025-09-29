<?php

namespace EventSoft\ServiceKit\Facades;

use EventSoft\ServiceKit\Enums\LogLevel;
use EventSoft\ServiceKit\Enums\LogType;
use EventSoft\ServiceKit\Managers\LogManager;
use Illuminate\Support\Facades\Facade;

/**
 * Log facade'i: merkezi, tek kanal üzerinden yapılandırılmış loglama sağlar.
 *
 * http: HTTP istek/yanıt logu gönderir
 * business: İş (domain) olayı logu gönderir
 * error: Hata logu gönderir (tek kanal)
 * performance: Performans metriği logu gönderir
 * security: Güvenlik olayı logu gönderir
 * audit: Denetim (audit) olayı logu gönderir
 * system: Sistem olayı logu gönderir
 * info: Bilgi seviyesinde basit mesaj loglar (tek kanal)
 * warning: Uyarı seviyesinde basit mesaj loglar (tek kanal)
 * debug: Debug seviyesinde basit mesaj loglar (tek kanal)
 * emergency: Acil seviyede mesaj loglar (tek kanal)
 * alert: Alarm seviyesinde mesaj loglar (tek kanal)
 * critical: Kritik seviyede mesaj loglar (tek kanal)
 * notice: Bildirim seviyesinde mesaj loglar (tek kanal)
 * log: Tip ve seviye belirterek genel log gönderir
 *
 * @method static void http(array $data) HTTP istek/yanıt logu gönderir
 * @method static void business(array $data) İş (domain) olayı logu gönderir
 * @method static void error(array $data) Hata logu gönderir (yapılandırılmış payload)
 * @method static void performance(array $data) Performans metriği logu gönderir
 * @method static void security(array $data) Güvenlik olayı logu gönderir
 * @method static void audit(array $data) Denetim (audit) olayı logu gönderir
 * @method static void system(array $data) Sistem olayı logu gönderir
 * @method static void info(mixed $message, array $context = []) Bilgi seviyesinde mesaj loglar
 * @method static void warning(mixed $message, array $context = []) Uyarı seviyesinde mesaj loglar
 * @method static void error(mixed $message, array $context = []) Hata seviyesinde mesaj loglar
 * @method static void debug(mixed $message, array $context = []) Debug seviyesinde mesaj loglar
 * @method static void emergency(mixed $message, array $context = []) Acil seviyede mesaj loglar
 * @method static void alert(mixed $message, array $context = []) Alarm seviyesinde mesaj loglar
 * @method static void critical(mixed $message, array $context = []) Kritik seviyede mesaj loglar
 * @method static void notice(mixed $message, array $context = []) Bildirim seviyesinde mesaj loglar
 * @method static void log(LogType $type, LogLevel $level, array $data) Genel log metodudur
 */
class Log extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return LogManager::class;
    }
}
