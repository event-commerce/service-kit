<?php

namespace EventSoft\ServiceKit\Facades;

use EventSoft\ServiceKit\Http\Middleware\CorrelationIdMiddleware as Middleware;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Symfony\Component\HttpFoundation\Response handle(\Illuminate\Http\Request $request, \Closure $next)
 */
class CorrelationIdMiddleware extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Middleware::class;
    }
}
