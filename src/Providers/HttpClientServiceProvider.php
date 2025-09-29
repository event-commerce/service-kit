<?php

namespace EventSoft\ServiceKit\Providers;

use EventSoft\ServiceKit\Contracts\LogPublisherInterface;
use EventSoft\ServiceKit\Contracts\PerformanceMonitorInterface;
use EventSoft\ServiceKit\Http\Client\HttpClient;
use EventSoft\ServiceKit\Managers\LogManager;
use EventSoft\ServiceKit\Managers\PerformanceManager;
use EventSoft\ServiceKit\Performance\PerformanceMonitor;
use EventSoft\ServiceKit\Publishers\RabbitMqLogPublisher;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

class HttpClientServiceProvider extends ServiceProvider
{
    public function register(): void
    {
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

        $this->app->singleton(HttpClient::class, function (Container $app): HttpClient {
            return new HttpClient(
                $app->make(LogManager::class),
                $app->make(PerformanceManager::class)
            );
        });
    }
}
