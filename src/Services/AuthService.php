<?php

namespace EventSoft\ServiceKit\Services;

use EventSoft\ServiceKit\Http\Client\BaseServiceClient;
use Psr\Http\Message\ResponseInterface;

class AuthService extends BaseServiceClient
{
    public function __construct()
    {
        parent::__construct('auth');
    }

    public function get(string $path, array $options = []): ResponseInterface
    {
        return parent::get($path, $options);
    }

    public function post(string $path, array $options = []): ResponseInterface
    {
        return parent::post($path, $options);
    }

    public function put(string $path, array $options = []): ResponseInterface
    {
        return parent::put($path, $options);
    }

    public function patch(string $path, array $options = []): ResponseInterface
    {
        return parent::patch($path, $options);
    }

    public function delete(string $path, array $options = []): ResponseInterface
    {
        return parent::delete($path, $options);
    }
}
