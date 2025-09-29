<?php

declare(strict_types=1);

namespace EventSoft\ServiceKit\Providers;

use EventSoft\ServiceKit\Http\Middleware\CorrelationIdMiddleware;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel;

final class MiddlewareServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Middleware'i singleton olarak kaydet
        $this->app->singleton(CorrelationIdMiddleware::class, function ($app) {
            return new CorrelationIdMiddleware($app->make(\EventSoft\ServiceKit\Correlation\CorrelationId::class));
        });
    }

    public function boot(): void
    {
        // Middleware'i global olarak kaydet (isteğe bağlı)
        if ($this->app->bound(Kernel::class)) {
            $kernel = $this->app->make(Kernel::class);
            $kernel->pushMiddleware(CorrelationIdMiddleware::class);
        }
    }
}
