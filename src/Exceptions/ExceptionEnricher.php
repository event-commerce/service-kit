<?php

namespace EventSoft\ServiceKit\Exceptions;

use EventSoft\ServiceKit\Managers\LogManager;
use EventSoft\ServiceKit\Context\ContextManager;
use Throwable;

class ExceptionEnricher
{
    public function __construct(
        private readonly LogManager $logManager
    ) {
    }

    public function enrichAndLog(Throwable $exception, array $context = []): void
    {
        $enrichedData = $this->enrichException($exception, $context);

        $this->logManager->logError([
            'exception' => [
                'class' => get_class($exception),
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $this->formatTrace($exception->getTrace()),
            ],
            'context' => ContextManager::getEnrichedContext(),
            'additional_context' => $context,
        ]);
    }

    private function enrichException(Throwable $exception, array $context): array
    {
        $enriched = [
            'exception_class' => get_class($exception),
            'exception_message' => $exception->getMessage(),
            'exception_code' => $exception->getCode(),
            'exception_file' => $exception->getFile(),
            'exception_line' => $exception->getLine(),
            'timestamp' => now()->toIso8601String(),
        ];

        if ($exception->getPrevious()) {
            $enriched['previous_exception'] = $this->enrichException($exception->getPrevious(), []);
        }

        return array_merge($enriched, $context);
    }

    private function formatTrace(array $trace): array
    {
        return array_map(function (array $frame) {
            return [
                'file' => $frame['file'] ?? null,
                'line' => $frame['line'] ?? null,
                'function' => $frame['function'] ?? null,
                'class' => $frame['class'] ?? null,
                'type' => $frame['type'] ?? null,
            ];
        }, $trace);
    }
}
