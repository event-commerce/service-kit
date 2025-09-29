<?php

namespace EventSoft\ServiceKit\Providers;

use EventSoft\ServiceKit\Correlation\CorrelationId;
use EventSoft\ServiceKit\Http\Middleware\ContextMiddleware;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Support\ServiceProvider;

class ContextServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CorrelationId::class, function (): CorrelationId {
            return new CorrelationId(
                config('service-kit.correlation.header', 'X-Correlation-Id'),
                (bool) config('service-kit.correlation.auto_generate', true)
            );
        });
    }

    public function boot(): void
    {
        /** @var HttpKernel $kernel */
        $kernel = $this->app->make(HttpKernel::class);
        // Her istekte correlation ve context yönetimi
        $kernel->pushMiddleware(ContextMiddleware::class);
    }
}
