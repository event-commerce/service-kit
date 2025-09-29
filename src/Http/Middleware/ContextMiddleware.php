<?php

namespace EventSoft\ServiceKit\Http\Middleware;

use Closure;
use EventSoft\ServiceKit\Context\ContextManager;
use EventSoft\ServiceKit\Correlation\CorrelationId;
use Illuminate\Http\Request;

class ContextMiddleware
{
    public function __construct(
        private readonly CorrelationId $correlation
    ) {
    }

    public function handle(Request $request, Closure $next)
    {
        $correlationId = $this->correlation->resolve($request);
        app()->instance('correlation-id', $correlationId);

        ContextManager::setRequest($request->header('X-Request-ID', uniqid('req_', true)));

        if ($request->hasSession()) {
            ContextManager::setSession($request->session()->getId());
        }

        if ($request->user()) {
            ContextManager::setUser((string) $request->user()->id);
        }

        if ($request->hasHeader('X-Tenant-ID')) {
            ContextManager::setTenant($request->header('X-Tenant-ID'));
        }

        $response = $next($request);

        $response->headers->set(
            $this->correlation->getHeaderName(),
            $correlationId
        );

        return $response;
    }
}

