<?php

namespace EventSoft\ServiceKit\Providers;

use EventSoft\ServiceKit\Http\Middleware\ContextMiddleware;
use EventSoft\ServiceKit\Http\Middleware\HttpLoggingMiddleware;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Support\ServiceProvider;

class HttpLoggingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (!config('service-kit.http_logging.enabled', false)) {
            return;
        }

        /** @var HttpKernel $kernel */
        $kernel = $this->app->make(HttpKernel::class);
        $kernel->pushMiddleware(ContextMiddleware::class);
        $kernel->pushMiddleware(HttpLoggingMiddleware::class);
    }
}
