<?php

namespace EventSoft\ServiceKit\CircuitBreaker;

use Illuminate\Support\Facades\Cache;
use RuntimeException;

class CircuitBreaker
{
    public function __construct(private readonly string $serviceKey)
    {
    }

    public function assertAvailable(): void
    {
        $state = $this->getState();
        if ($state['open_until'] !== null && time() < $state['open_until']) {
            throw new RuntimeException('Circuit open for service: ' . $this->serviceKey);
        }
    }

    public function onSuccess(): void
    {
        $this->saveState(['failures' => 0, 'open_until' => null]);
    }

    public function onFailure(): void
    {
        $state = $this->getState();
        $failures = $state['failures'] + 1;
        $threshold = (int) config("service-kit.services.{$this->serviceKey}.circuit.fail_threshold", 5);
        $cooldown = (int) config("service-kit.services.{$this->serviceKey}.circuit.cooldown_seconds", 30);

        if ($failures >= $threshold) {
            $this->saveState([
                'failures' => 0,
                'open_until' => time() + $cooldown,
            ]);
        } else {
            $this->saveState([
                'failures' => $failures,
                'open_until' => $state['open_until'],
            ]);
        }
    }

    private function getState(): array
    {
        $key = $this->cacheKey();
        return Cache::get($key, ['failures' => 0, 'open_until' => null]);
    }

    private function saveState(array $state): void
    {
        $key = $this->cacheKey();
        Cache::put($key, $state, now()->addHours(6));
    }

    private function cacheKey(): string
    {
        return 'service_kit:circuit:' . $this->serviceKey;
    }
}
