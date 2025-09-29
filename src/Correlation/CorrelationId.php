<?php

namespace EventSoft\ServiceKit\Correlation;

use Illuminate\Http\Request;

class CorrelationId
{
    public function __construct(
        private readonly string $headerName,
        private readonly bool $autoGenerate
    ) {
    }

    public function resolve(Request $request): string
    {
        $id = (string) $request->headers->get($this->headerName, '');
        if ($id !== '') {
            return $id;
        }

        if ($this->autoGenerate) {
            return $this->generate();
        }

        return '';
    }

    public function generate(): string
    {
        $bytes = random_bytes(16);
        $hex = bin2hex($bytes);
        return substr($hex, 0, 8) . '-' . substr($hex, 8, 4) . '-' . substr($hex, 12, 4) . '-' . substr($hex, 16, 4) . '-' . substr($hex, 20);
    }

    public function getHeaderName(): string
    {
        return $this->headerName;
    }

    public function get(): string
    {
        $request = request();
        if ($request) {
            return $this->resolve($request);
        }
        
        return $this->autoGenerate ? $this->generate() : '';
    }
}
