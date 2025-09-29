<?php

namespace EventSoft\ServiceKit\Http\Middleware;

use Closure;
use EventSoft\ServiceKit\Managers\LogManager;
use EventSoft\ServiceKit\Correlation\CorrelationId;
use Illuminate\Http\Request;

class HttpLoggingMiddleware
{
    public function __construct(
        private readonly LogManager $logManager,
        private readonly CorrelationId $correlation
    ) {
    }

    public function handle(Request $request, Closure $next)
    {
        if (!config('service-kit.http_logging.enabled', false)) {
            return $next($request);
        }

        if (!$this->shouldLogRequest($request)) {
            return $next($request);
        }

        $correlationId = $this->correlation->resolve($request);
        $request->headers->set($this->correlation->getHeaderName(), $correlationId);

        $start = microtime(true);
        $response = $next($request);
        $durationMs = (int) ((microtime(true) - $start) * 1000);

        $this->logRequest($request, $response, $correlationId, $durationMs);

        return $response;
    }

    private function shouldLogRequest(Request $request): bool
    {
        $path = $request->getPathInfo();
        $method = strtoupper($request->getMethod());

        $endpointConfig = config('service-kit.http_logging.endpoints', '*');
        if ($endpointConfig === '*') {
            return true;
        }

        // Çoklu pattern desteği: "GET:/api/*,POST:/v1/auth/*"
        $patterns = is_array($endpointConfig) ? $endpointConfig : explode(',', (string) $endpointConfig);
        foreach ($patterns as $pattern) {
            $pattern = trim($pattern);
            if ($pattern === '') { continue; }

            if (str_contains($pattern, ':')) {
                [$pMethod, $pPath] = explode(':', $pattern, 2);
                if (strtoupper($pMethod) !== $method) { continue; }
                if (fnmatch($pPath, $path)) { return true; }
            } else {
                if (fnmatch($pattern, $path)) { return true; }
            }
        }

        // include/exclude eski alanlarla geriye dönük uyumluluk
        $includeEndpoints = config('service-kit.http_logging.include_endpoints', []);
        if (!empty($includeEndpoints)) {
            foreach ($includeEndpoints as $p) {
                if (fnmatch($p, $path)) { return true; }
            }
            return false;
        }

        $excludeEndpoints = config('service-kit.http_logging.exclude_endpoints', []);
        if (!empty($excludeEndpoints)) {
            foreach ($excludeEndpoints as $p) {
                if (fnmatch($p, $path)) { return false; }
            }
        }

        return true;
    }

    private function logRequest(Request $request, $response, string $correlationId, int $durationMs): void
    {
        $data = [
            'correlation_id' => $correlationId,
            'method' => $request->getMethod(),
            'path' => $request->getPathInfo(),
            'status' => method_exists($response, 'getStatusCode') ? $response->getStatusCode() : null,
            'duration_ms' => $durationMs,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'headers' => $this->getFilteredHeaders($request),
            'query_params' => $request->query->all(),
            'is_slow' => $durationMs >= (int) config('service-kit.http_logging.slow_request_threshold_ms', 2000),
        ];

        $maxBodyBytes = config('service-kit.http_logging.max_body_bytes', 2048);
        if (config('service-kit.http_logging.log_request_body', true) && $request->getContent() && strlen($request->getContent()) <= $maxBodyBytes) {
            $data['request_body'] = $request->getContent();
        }

        $this->logManager->logHttp($data);
    }

    private function getFilteredHeaders(Request $request): array
    {
        $headers = $request->headers->all();
        $filtered = [];

        $sensitive = array_map('strtolower', (array) config('service-kit.http_logging.sensitive_headers', []));

        foreach ($headers as $key => $value) {
            if (!in_array(strtolower($key), $sensitive, true)) {
                $filtered[$key] = is_array($value) ? $value[0] : $value;
            }
        }

        return $filtered;
    }
}

