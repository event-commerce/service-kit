<?php

namespace EventSoft\ServiceKit\Http\Client;

use EventSoft\ServiceKit\Enums\LogLevel;
use EventSoft\ServiceKit\Enums\LogType;
use EventSoft\ServiceKit\Managers\LogManager;
use EventSoft\ServiceKit\Managers\PerformanceManager;
use EventSoft\ServiceKit\Context\ContextManager;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpClient
{
    private Client $client;
    private array $config;
    private int $retryCount = 0;
    private int $timeout = 30;
    private float $jitter = 0.0;

    public function __construct(
        private readonly LogManager $logManager,
        private readonly PerformanceManager $performanceManager
    ) {
        $this->config = config('service-kit.http_client', []);
        $this->client = $this->createClient();
    }

    public function retry(int $count): self
    {
        $this->retryCount = $count;
        return $this;
    }

    public function timeout(int $seconds): self
    {
        $this->timeout = $seconds;
        return $this;
    }

    public function jitter(float $jitter): self
    {
        $this->jitter = $jitter;
        return $this;
    }

    public function get(string $url, array $options = []): ResponseInterface
    {
        return $this->request('GET', $url, $options);
    }

    public function post(string $url, array $options = []): ResponseInterface
    {
        return $this->request('POST', $url, $options);
    }

    public function put(string $url, array $options = []): ResponseInterface
    {
        return $this->request('PUT', $url, $options);
    }

    public function patch(string $url, array $options = []): ResponseInterface
    {
        return $this->request('PATCH', $url, $options);
    }

    public function delete(string $url, array $options = []): ResponseInterface
    {
        return $this->request('DELETE', $url, $options);
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $operation = "http_{$method}_" . parse_url($url, PHP_URL_HOST);
        $this->performanceManager->start($operation);

        $options = array_merge([
            'timeout' => $this->timeout,
            'headers' => $this->getDefaultHeaders(),
        ], $options);

        $attempt = 0;
        $lastException = null;

        while ($attempt <= $this->retryCount) {
            try {
                $response = $this->client->request($method, $url, $options);

                $this->logRequest($method, $url, $options, $response, $attempt);
                $this->performanceManager->stop($operation, [
                    'method' => $method,
                    'url' => $url,
                    'status' => $response->getStatusCode(),
                    'attempt' => $attempt + 1,
                ]);

                return $response;
            } catch (GuzzleException $e) {
                $lastException = $e;
                $attempt++;

                if ($attempt <= $this->retryCount) {
                    $delay = $this->calculateDelay($attempt);
                    usleep($delay * 1000000);
                }
            }
        }

        $this->logRequest($method, $url, $options, null, $attempt, $lastException);
        $this->performanceManager->stop($operation, [
            'method' => $method,
            'url' => $url,
            'error' => $lastException?->getMessage(),
            'attempts' => $attempt,
        ]);

        throw $lastException;
    }

    private function createClient(): Client
    {
        $stack = HandlerStack::create();

        $stack->push(Middleware::retry(
            $this->retryDecider(),
            $this->retryDelay()
        ));

        $stack->push($this->loggingMiddleware());

        return new Client([
            'handler' => $stack,
            'timeout' => $this->timeout,
        ]);
    }

    private function retryDecider(): callable
    {
        return function ($retries, RequestInterface $request, ResponseInterface $response = null, $exception = null) {
            if ($retries >= $this->retryCount) {
                return false;
            }

            if ($exception) {
                return true;
            }

            if ($response && $response->getStatusCode() >= 500) {
                return true;
            }

            return false;
        };
    }

    private function retryDelay(): callable
    {
        return function ($numberOfRetries) {
            $baseDelay = pow(2, $numberOfRetries) * 1000;
            $jitterAmount = $baseDelay * $this->jitter;
            $delay = $baseDelay + ($jitterAmount * (mt_rand() / mt_getrandmax()));

            return (int) $delay;
        };
    }

    private function loggingMiddleware(): callable
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                return $handler($request, $options)->then(
                    function (ResponseInterface $response) use ($request, $options) {
                        $this->logRequest(
                            $request->getMethod(),
                            (string) $request->getUri(),
                            $options,
                            $response
                        );
                        return $response;
                    },
                    function ($exception) use ($request, $options) {
                        $this->logRequest(
                            $request->getMethod(),
                            (string) $request->getUri(),
                            $options,
                            null,
                            0,
                            $exception
                        );
                        throw $exception;
                    }
                );
            };
        };
    }

    private function getDefaultHeaders(): array
    {
        $headers = [
            'User-Agent' => 'EventSoft-ServiceKit/1.0',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        if (app()->has('correlation-id')) {
            $headers['X-Correlation-Id'] = app()->get('correlation-id');
        }

        if (ContextManager::getTenantId()) {
            $headers['X-Tenant-ID'] = ContextManager::getTenantId();
        }

        return $headers;
    }

    private function calculateDelay(int $attempt): float
    {
        $baseDelay = pow(2, $attempt) * 1000;
        $jitterAmount = $baseDelay * $this->jitter;
        $delay = $baseDelay + ($jitterAmount * (mt_rand() / mt_getrandmax()));

        return $delay / 1000;
    }

    private function logRequest(
        string $method,
        string $url,
        array $options,
        ?ResponseInterface $response = null,
        int $attempt = 0,
        ?\Throwable $exception = null
    ): void {
        $data = [
            'method' => $method,
            'url' => $url,
            'attempt' => $attempt + 1,
            'context' => ContextManager::getEnrichedContext(),
        ];

        if ($response) {
            $data['status'] = $response->getStatusCode();
            $data['response_headers'] = $response->getHeaders();
        }

        if ($exception) {
            $data['error'] = $exception->getMessage();
            $data['error_class'] = get_class($exception);
        }

        if ($exception) {
            $this->logManager->log(LogType::ERROR, LogLevel::ERROR, $data);
        } else {
            $this->logManager->log(LogType::HTTP, LogLevel::INFO, $data);
        }
    }
}

