<?php

namespace EventSoft\ServiceKit\Providers;

use EventSoft\ServiceKit\Contracts\LogPublisherInterface;
use EventSoft\ServiceKit\Managers\LogManager;
use EventSoft\ServiceKit\Publishers\RabbitMqLogPublisher;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

class LoggingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/service-kit.php' => config_path('service-kit.php'),
        ], 'service-kit-config');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/service-kit.php',
            'service-kit'
        );

        $this->app->bind(LogPublisherInterface::class, function (Container $app): LogPublisherInterface {
            $driver = (string) config('service-kit.logging.driver', 'rabbitmq');
            return match ($driver) {
                'rabbitmq' => new RabbitMqLogPublisher((array) config('service-kit.rabbitmq')),
                default => new RabbitMqLogPublisher((array) config('service-kit.rabbitmq')),
            };
        });

        $this->app->singleton(LogManager::class, function (Container $app): LogManager {
            return new LogManager($app->make(LogPublisherInterface::class));
        });
    }
}
