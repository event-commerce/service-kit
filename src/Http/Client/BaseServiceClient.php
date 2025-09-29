<?php

namespace EventSoft\ServiceKit\Http\Client;

use EventSoft\ServiceKit\CircuitBreaker\CircuitBreaker;
use EventSoft\ServiceKit\Facades\Http;
use Psr\Http\Message\ResponseInterface;

abstract class BaseServiceClient
{
    protected ?int $overrideRetry = null;
    protected ?int $overrideTimeout = null;
    protected ?float $overrideJitter = null;
    /** @var array<string,string> */
    protected array $extraHeaders = [];
    /** @var array<string,mixed> */
    protected array $extraOptions = [];

    public function __construct(
        protected readonly string $serviceKey
    ) {
    }

    // Chainable configuration
    public function retry(int $count): static
    {
        $this->overrideRetry = $count;
        return $this;
    }

    public function timeout(int $seconds): static
    {
        $this->overrideTimeout = $seconds;
        return $this;
    }

    public function jitter(float $jitter): static
    {
        $this->overrideJitter = $jitter;
        return $this;
    }

    /** @param array<string,string> $headers */
    public function withHeaders(array $headers): static
    {
        $this->extraHeaders = array_merge($this->extraHeaders, $headers);
        return $this;
    }

    /** @param array<string,mixed> $options */
    public function withOptions(array $options): static
    {
        $this->extraOptions = array_replace_recursive($this->extraOptions, $options);
        return $this;
    }

    // Health check
    public function health(): bool
    {
        $path = (string) config("service-kit.services.{$this->serviceKey}.health_path", '/health');
        try {
            $res = $this->get($path);
            return $res->getStatusCode() < 500;
        } catch (\Throwable) {
            return false;
        }
    }

    protected function baseUrl(): string
    {
        return rtrim(config("service-kit.services.{$this->serviceKey}.base_url"), '/');
    }

    protected function timeoutDefault(): int
    {
        return (int) config("service-kit.services.{$this->serviceKey}.timeout", 10);
    }

    protected function retryDefault(): int
    {
        return (int) config("service-kit.services.{$this->serviceKey}.retry", 2);
    }

    protected function jitterDefault(): float
    {
        return (float) config("service-kit.services.{$this->serviceKey}.jitter", 0.2);
    }

    protected function http(): Http
    {
        $retry = $this->overrideRetry ?? $this->retryDefault();
        $timeout = $this->overrideTimeout ?? $this->timeoutDefault();
        $jitter = $this->overrideJitter ?? $this->jitterDefault();

        return Http::retry($retry)->timeout($timeout)->jitter($jitter);
    }

    protected function buildUrl(string $path): string
    {
        return $this->baseUrl() . '/' . ltrim($path, '/');
    }

    /** @param array<string,mixed> $options */
    protected function get(string $path, array $options = []): ResponseInterface
    {
        return $this->send('get', $path, $options);
    }

    /** @param array<string,mixed> $options */
    protected function post(string $path, array $options = []): ResponseInterface
    {
        return $this->send('post', $path, $options);
    }

    /** @param array<string,mixed> $options */
    protected function put(string $path, array $options = []): ResponseInterface
    {
        return $this->send('put', $path, $options);
    }

    /** @param array<string,mixed> $options */
    protected function patch(string $path, array $options = []): ResponseInterface
    {
        return $this->send('patch', $path, $options);
    }

    /** @param array<string,mixed> $options */
    protected function delete(string $path, array $options = []): ResponseInterface
    {
        return $this->send('delete', $path, $options);
    }

    /** @param array<string,mixed> $options */
    private function send(string $method, string $path, array $options): ResponseInterface
    {
        $cb = new CircuitBreaker($this->serviceKey);
        $cb->assertAvailable();

        $options = $this->mergeOptions($options);

        try {
            $response = $this->http()->{$method}($this->buildUrl($path), $options);
            $cb->onSuccess();
            return $response;
        } catch (\Throwable $e) {
            $cb->onFailure();
            throw $e;
        } finally {
            $this->resetState();
        }
    }

    /** @param array<string,mixed> $options */
    private function mergeOptions(array $options): array
    {
        // Merge headers
        $headers = array_merge($options['headers'] ?? [], $this->extraHeaders);
        if (!empty($headers)) {
            $options['headers'] = $headers;
        }
        // Merge other options
        if (!empty($this->extraOptions)) {
            $options = array_replace_recursive($options, $this->extraOptions);
        }
        return $options;
    }

    private function resetState(): void
    {
        $this->overrideRetry = null;
        $this->overrideTimeout = null;
        $this->overrideJitter = null;
        $this->extraHeaders = [];
        $this->extraOptions = [];
    }
}
