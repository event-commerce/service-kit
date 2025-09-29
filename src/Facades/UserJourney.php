<?php

namespace EventSoft\ServiceKit\Facades;

use EventSoft\ServiceKit\UserJourney\UserJourneyTracker;
use Illuminate\Support\Facades\Facade;

/**
 * UserJourney facade'i: kullanıcı yolculuğu olaylarını loglar.
 *
 * trackAction: Kullanıcı aksiyonunu kaydeder
 * trackNavigation: Sayfalar arası geçişi kaydeder
 * trackInteraction: UI element etkileşimini kaydeder
 * trackBusinessEvent: Genel iş olayını kaydeder
 * trackError: Yolculuk sırasında hatayı kaydeder
 * trackPerformance: Yolculuk içi performansı kaydeder
 *
 * @method static void trackAction(string $action, array $data = []) Kullanıcı aksiyonu
 * @method static void trackNavigation(string $from, string $to, array $data = []) Navigasyon
 * @method static void trackInteraction(string $element, string $interaction, array $data = []) Etkileşim
 * @method static void trackBusinessEvent(string $event, array $data = []) İş olayı
 * @method static void trackError(string $error, array $data = []) Hata
 * @method static void trackPerformance(string $operation, float $duration, array $data = []) Performans
 */
class UserJourney extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return UserJourneyTracker::class;
    }
}

