<?php

namespace EventSoft\ServiceKit\Providers;

use EventSoft\ServiceKit\Queue\QueueManager;
use Illuminate\Support\ServiceProvider;

class QueueServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(QueueManager::class, function () {
            return new QueueManager();
        });
    }
}
