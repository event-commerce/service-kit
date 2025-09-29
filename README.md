# EventSoft Service Kit

EventSoft mikroservis mimarisi için paylaşılan altyapı paketi. Loglama, performans izleme, korelasyon ID yönetimi ve user journey takibi sağlar.

## Özellikler

- 🔧 **Config-Based Architecture**: Tüm ayarlar config dosyasından yönetilir
- 🎯 **Endpoint-Specific Logging**: Belirli endpoint'lerde HTTP loglama
- 📊 **Merkezi Loglama Sistemi**: RabbitMQ üzerinden log-messages kanalına log gönderimi
- 🌐 **HTTP Request/Response Logging**: Otomatik HTTP istek/yanıt loglama
- ⚡ **Performance Monitoring**: Performans metrikleri izleme
- 📈 **Business Event Logging**: İş olayları loglama
- 🔒 **Error Handling**: Gelişmiş hata yönetimi
- 🎨 **SOLID Principles**: Clean code ve yüksek OOP standartları
- 🏗️ **Design Patterns**: Factory, Manager, Facade pattern'leri
- 🎭 **User Journey Tracking**: Kullanıcı yolculuğu takibi
- 🏢 **Multi-tenant Context**: Tenant-aware loglama

## Kurulum

### Composer ile Kurulum

```bash
composer require eventsoft/service-kit
```

### Path Repository ile Kurulum (Geliştirme)

`composer.json` dosyanıza path repository ekleyin:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../service-kit",
            "options": {
                "symlink": true
            }
        }
    ],
    "require": {
        "eventsoft/service-kit": "*"
    }
}
```

## Yapılandırma

Config dosyasını yayınlayın:

```bash
php artisan vendor:publish --tag=service-kit-config
```

### Environment Variables

```env
# RabbitMQ Configuration
RABBITMQ_HOST=rabbitmq
RABBITMQ_PORT=5672
RABBITMQ_USER=guest
RABBITMQ_PASSWORD=guest
RABBITMQ_VHOST=/
RABBITMQ_EXCHANGE=logs
RABBITMQ_ROUTING_KEY=log-messages

# Service Kit Configuration
SERVICE_KIT_LOGGING_ENABLED=true
SERVICE_KIT_HTTP_LOGGING_ENABLED=false
SERVICE_KIT_PERFORMANCE_ENABLED=false
```

## Kullanım

### Facade ile Kullanım

```php
use EventSoft\ServiceKit\Facades\ServiceKit;

// HTTP Loglama
ServiceKit::logHttp([
    'method' => 'GET',
    'path' => '/api/users',
    'status' => 200,
    'duration_ms' => 150
]);

// Business Event Loglama
ServiceKit::logBusiness([
    'event' => 'user_registered',
    'user_id' => 123,
    'tenant_id' => 'tenant-1'
]);

// Performans İzleme
ServiceKit::start('database_query');
// ... işlemler ...
$duration = ServiceKit::stop('database_query', ['query' => 'SELECT * FROM users']);
```

### Manager ile Kullanım

```php
use EventSoft\ServiceKit\Managers\LogManager;
use EventSoft\ServiceKit\Managers\PerformanceManager;

$logManager = app(LogManager::class);
$performanceManager = app(PerformanceManager::class);

// Loglama
$logManager->logError([
    'error' => 'Database connection failed',
    'context' => ['host' => 'localhost']
]);

// Performans İzleme
$performanceManager->start('api_call');
// ... işlemler ...
$performanceManager->stop('api_call', ['endpoint' => '/api/users']);
```

### Context Management

```php
use EventSoft\ServiceKit\Support\Context\ContextManager;

// Context ayarlama
ContextManager::setTenant('tenant-1');
ContextManager::setUser('user-123');
ContextManager::setSession('session-456');

// Context okuma
$tenantId = ContextManager::getTenantId();
$userId = ContextManager::getUserId();
```

### User Journey Tracking

```php
use EventSoft\ServiceKit\Support\UserJourney\UserJourneyTracker;

$tracker = app(UserJourneyTracker::class);

// Kullanıcı aksiyonu takibi
$tracker->trackAction('button_click', [
    'button_id' => 'submit-form',
    'page' => '/checkout'
]);

// Navigasyon takibi
$tracker->trackNavigation('/products', '/checkout', [
    'product_count' => 3
]);
```

### Exception Handling

```php
use EventSoft\ServiceKit\Support\Exception\ExceptionEnricher;

$enricher = app(ExceptionEnricher::class);

try {
    // Risky operation
} catch (\Throwable $e) {
    $enricher->enrichAndLog($e, [
        'operation' => 'user_registration',
        'user_data' => $userData
    ]);
    throw $e;
}
```

## Middleware Kullanımı

### HTTP Logging Middleware

```php
// app/Http/Kernel.php
protected $middleware = [
    \EventSoft\ServiceKit\Http\Middleware\HttpLoggingMiddleware::class,
    \EventSoft\ServiceKit\Http\Middleware\ContextMiddleware::class,
];
```

### Context Middleware

Context middleware'i korelasyon ID ve tenant context'ini otomatik olarak ayarlar.

## Log Types

Paket aşağıdaki log tiplerini destekler:

- **HTTP**: HTTP istek/yanıt logları
- **BUSINESS**: İş olayları
- **ERROR**: Hata logları
- **PERFORMANCE**: Performans metrikleri
- **SECURITY**: Güvenlik olayları
- **AUDIT**: Denetim logları
- **SYSTEM**: Sistem olayları

## Log Levels

- **EMERGENCY**: 0
- **ALERT**: 1
- **CRITICAL**: 2
- **ERROR**: 3
- **WARNING**: 4
- **NOTICE**: 5
- **INFO**: 6
- **DEBUG**: 7

## Mimari

### SOLID Prensipleri

- **Single Responsibility**: Her sınıf tek bir sorumluluğa sahip
- **Open/Closed**: Genişletmeye açık, değişikliğe kapalı
- **Liskov Substitution**: Alt sınıflar üst sınıfların yerine geçebilir
- **Interface Segregation**: Küçük, odaklanmış arayüzler
- **Dependency Inversion**: Soyutlamalara bağımlılık

### Design Patterns

- **Factory Pattern**: LogEntryFactory
- **Manager Pattern**: LogManager, PerformanceManager
- **Facade Pattern**: ServiceKit facade
- **Strategy Pattern**: LogPublisher implementations

## Geliştirme

### Test

```bash
composer test
```

### Code Style

```bash
composer cs-fix
```

## Lisans

MIT License

