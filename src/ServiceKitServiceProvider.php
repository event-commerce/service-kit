<?php

namespace EventSoft\ServiceKit;

use EventSoft\ServiceKit\Contracts\LogPublisherInterface;
use EventSoft\ServiceKit\Contracts\PerformanceMonitorInterface;
use EventSoft\ServiceKit\Managers\LogManager;
use EventSoft\ServiceKit\Managers\PerformanceManager;
use EventSoft\ServiceKit\Publishers\RabbitMqLogPublisher;
use EventSoft\ServiceKit\Context\ContextManager;
use EventSoft\ServiceKit\Correlation\CorrelationId;
use EventSoft\ServiceKit\Exceptions\ExceptionEnricher;
use EventSoft\ServiceKit\Performance\PerformanceMonitor;
use EventSoft\ServiceKit\UserJourney\UserJourneyTracker;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

class ServiceKitServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/service-kit.php', 'service-kit');

        // Correlation ID Singleton
        $this->app->singleton(CorrelationId::class, function (): CorrelationId {
            return new CorrelationId(
                config('service-kit.correlation.header', 'X-Correlation-Id'),
                (bool) config('service-kit.correlation.auto_generate', true)
            );
        });

        // Log Publisher Binding
        $this->app->bind(LogPublisherInterface::class, function (Container $app): LogPublisherInterface {
            $driver = (string) config('service-kit.logging.driver', 'rabbitmq');
            
            return match ($driver) {
                'rabbitmq' => new RabbitMqLogPublisher((array) config('service-kit.rabbitmq')),
                default => new RabbitMqLogPublisher((array) config('service-kit.rabbitmq')),
            };
        });

        // Performance Monitor Binding
        $this->app->bind(PerformanceMonitorInterface::class, function (): PerformanceMonitorInterface {
            return new PerformanceMonitor();
        });

        // Managers
        $this->app->singleton(LogManager::class, function (Container $app): LogManager {
            return new LogManager($app->make(LogPublisherInterface::class));
        });

        $this->app->singleton(PerformanceManager::class, function (Container $app): PerformanceManager {
            return new PerformanceManager($app->make(PerformanceMonitorInterface::class));
        });

        // Exception Enricher
        $this->app->singleton(ExceptionEnricher::class, function (Container $app): ExceptionEnricher {
            return new ExceptionEnricher($app->make(LogManager::class));
        });

        // User Journey Tracker
        $this->app->singleton(UserJourneyTracker::class, function (Container $app): UserJourneyTracker {
            return new UserJourneyTracker($app->make(LogManager::class));
        });

        // Main ServiceKit class
        $this->app->singleton('service-kit', function (Container $app): ServiceKit {
            return new ServiceKit(
                $app->make(LogManager::class),
                $app->make(PerformanceManager::class)
            );
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/service-kit.php' => config_path('service-kit.php'),
        ], 'service-kit-config');
    }
}
