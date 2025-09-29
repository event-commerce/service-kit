<?php

/**
 * Service-Kit Configuration
 * 
 * Queue Usage:
 * - KitLog::info() → logs channel → log-messages queue → Graylog
 * - KitQueue::send('events', $payload) → events channel → events queue → Events consumer
 * - KitQueue::send('metrics', $payload) → metrics channel → metrics queue → Metrics consumer
 * 
 * Dead Letter Exchange:
 * - TTL süresi dolan veya reddedilen mesajlar dlx exchange'ine gider
 * - dlx exchange'i manuel olarak oluşturulmalı ve consumer'ları olmalı
 */

return [
    'correlation' => [
        'header' => 'X-Correlation-Id',
        'auto_generate' => true,
    ],

    'http_logging' => [
        'enabled' => false,
        'endpoints' => env('SERVICE_KIT_HTTP_LOGGING_ENDPOINTS', '*'),
        'include_endpoints' => [],
        'exclude_endpoints' => [],
        'max_body_bytes' => 2048,
        'sensitive_headers' => [
            'authorization', 'cookie', 'x-api-key', 'x-auth-token'
        ],
        'log_request_body' => true,
        'log_response_body' => false,
        'slow_request_threshold_ms' => 2000,
    ],

    'logging' => [
        'enabled' => true,
        'driver' => 'rabbitmq',
        'routing_key' => 'log-messages',
        'channel' => env('SERVICE_KIT_LOG_CHANNEL', 'service-kit'),
    ],

    'rabbitmq' => [
        'host' => env('RABBITMQ_HOST', 'rabbitmq'),
        'port' => (int) env('RABBITMQ_PORT', 5672),
        'user' => env('RABBITMQ_USER', 'guest'),
        'password' => env('RABBITMQ_PASSWORD', 'guest'),
        'vhost' => env('RABBITMQ_VHOST', '/'),
        'exchange' => env('RABBITMQ_EXCHANGE', 'logs'),
        'exchange_type' => env('RABBITMQ_EXCHANGE_TYPE', 'topic'),
        'routing_key' => env('RABBITMQ_ROUTING_KEY', 'log-messages'),
        'queue' => env('RABBITMQ_QUEUE', 'log-messages'),
        'persistent' => (bool) env('RABBITMQ_PERSISTENT', true),
        'dead_letter_exchange' => env('RABBITMQ_DEAD_LETTER_EXCHANGE', 'dlx'),
        'ssl' => [
            'enabled' => (bool) env('RABBITMQ_SSL_ENABLED', false),
        ],
    ],

    'queues' => [
        'channels' => [
            'logs' => env('SERVICE_KIT_QUEUE_LOG_CHANNEL', 'log-messages'),      // Graylog'a gider
            'metrics' => env('SERVICE_KIT_QUEUE_METRICS_CHANNEL', 'metrics'),    // Metrics consumer'a gider
            'events' => env('SERVICE_KIT_QUEUE_EVENTS_CHANNEL', 'events'),       // Events consumer'a gider
        ],
        'settings' => [
            'logs' => [
                'durable' => (bool) env('SERVICE_KIT_QUEUE_LOG_DURABLE', true),
                'ttl' => (int) env('SERVICE_KIT_QUEUE_LOG_TTL', 86400000),
                'max_length' => (int) env('SERVICE_KIT_QUEUE_LOG_MAX_LENGTH', 10000),
            ],
            'metrics' => [
                'durable' => (bool) env('SERVICE_KIT_QUEUE_METRICS_DURABLE', true),
                'ttl' => (int) env('SERVICE_KIT_QUEUE_METRICS_TTL', 604800000), // 7 gün (168 saat)
                'max_length' => (int) env('SERVICE_KIT_QUEUE_METRICS_MAX_LENGTH', 50000),
            ],
            'events' => [
                'durable' => (bool) env('SERVICE_KIT_QUEUE_EVENTS_DURABLE', true),
                'ttl' => (int) env('SERVICE_KIT_QUEUE_EVENTS_TTL', 86400000), // 1 gün (24 saat)
                'max_length' => (int) env('SERVICE_KIT_QUEUE_EVENTS_MAX_LENGTH', 100000),
            ],
        ],
        'delivery_mode' => (int) env('SERVICE_KIT_QUEUE_DELIVERY_MODE', 2),
    ],

    'service' => [
        'name' => env('SERVICE_KIT_SERVICE_NAME', env('APP_NAME', 'unknown-service')),
        'instance_id' => env('APP_INSTANCE_ID'),
    ],

    'performance' => [
        'enabled' => false,
    ],

    'context' => [
        'tenant_id_key' => 'tenant_id',
        'user_id_key' => 'user_id',
        'session_id_key' => 'session_id',
        'request_id_key' => 'request_id',
    ],

    'headers' => [
        'tenant' => env('HEADER_TENANT_ID', 'X-Tenant-ID'),
        'user' => env('HEADER_USER_ID', 'X-User-ID'),
        'customer' => env('HEADER_CUSTOMER_ID', 'X-Customer-ID'),
        'seller' => env('HEADER_SELLER_ID', 'X-Seller-ID'),
        'admin' => env('HEADER_ADMIN_ID', 'X-Admin-ID'),
        'session' => env('HEADER_SESSION_ID', 'X-Session-ID'),
        'request' => env('HEADER_REQUEST_ID', 'X-Request-ID'),
    ],

    'services' => [
        'user' => [
            'base_url' => env('USER_SERVICE_BASE_URL', 'https://user-service'),
            'timeout' => (int) env('USER_SERVICE_TIMEOUT', 10),
            'retry' => (int) env('USER_SERVICE_RETRY', 2),
            'jitter' => (float) env('USER_SERVICE_JITTER', 0.2),
            'circuit' => [
                'fail_threshold' => (int) env('USER_SERVICE_CIRCUIT_FAILS', 5),
                'cooldown_seconds' => (int) env('USER_SERVICE_CIRCUIT_COOLDOWN', 30),
            ],
            'health_path' => env('USER_SERVICE_HEALTH_PATH', '/up'),
        ],
        'auth' => [
            'base_url' => env('AUTH_SERVICE_BASE_URL', 'https://auth-service'),
            'timeout' => (int) env('AUTH_SERVICE_TIMEOUT', 10),
            'retry' => (int) env('AUTH_SERVICE_RETRY', 2),
            'jitter' => (float) env('AUTH_SERVICE_JITTER', 0.2),
            'circuit' => [
                'fail_threshold' => (int) env('AUTH_SERVICE_CIRCUIT_FAILS', 5),
                'cooldown_seconds' => (int) env('AUTH_SERVICE_CIRCUIT_COOLDOWN', 30),
            ],
            'health_path' => env('AUTH_SERVICE_HEALTH_PATH', '/up'),
        ],
    ],
];
