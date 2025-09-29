<?php

declare(strict_types=1);

namespace EventSoft\ServiceKit\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use EventSoft\ServiceKit\Correlation\CorrelationId;

final readonly class CorrelationIdMiddleware
{
    public function __construct(
        private CorrelationId $correlationId
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        // CorrelationId'yi request'ten al veya oluştur
        $correlationId = $this->correlationId->resolve($request);
        
        // Response header'ına ekle
        $response = $next($request);
        $response->headers->set('X-Correlation-Id', $correlationId);
        
        return $response;
    }
}
