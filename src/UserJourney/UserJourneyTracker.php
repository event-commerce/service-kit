<?php

namespace EventSoft\ServiceKit\UserJourney;

use EventSoft\ServiceKit\Managers\LogManager;
use EventSoft\ServiceKit\Context\ContextManager;

class UserJourneyTracker
{
    public function __construct(
        private readonly LogManager $logManager
    ) {
    }

    public function trackAction(string $action, array $data = []): void
    {
        $this->logManager->logBusiness([
            'event_type' => 'user_action',
            'action' => $action,
            'context' => ContextManager::getEnrichedContext(),
            ...$data,
        ]);
    }

    public function trackNavigation(string $from, string $to, array $data = []): void
    {
        $this->logManager->logBusiness([
            'event_type' => 'user_navigation',
            'from' => $from,
            'to' => $to,
            'context' => ContextManager::getEnrichedContext(),
            ...$data,
        ]);
    }

    public function trackInteraction(string $element, string $interaction, array $data = []): void
    {
        $this->logManager->logBusiness([
            'event_type' => 'user_interaction',
            'element' => $element,
            'interaction' => $interaction,
            'context' => ContextManager::getEnrichedContext(),
            ...$data,
        ]);
    }

    public function trackBusinessEvent(string $event, array $data = []): void
    {
        $this->logManager->logBusiness([
            'event_type' => 'business_event',
            'event' => $event,
            'context' => ContextManager::getEnrichedContext(),
            ...$data,
        ]);
    }

    public function trackError(string $error, array $data = []): void
    {
        $this->logManager->logError([
            'event_type' => 'user_journey_error',
            'error' => $error,
            'context' => ContextManager::getEnrichedContext(),
            ...$data,
        ]);
    }

    public function trackPerformance(string $operation, float $duration, array $data = []): void
    {
        $this->logManager->logPerformance([
            'event_type' => 'user_journey_performance',
            'operation' => $operation,
            'duration_ms' => $duration,
            'context' => ContextManager::getEnrichedContext(),
            ...$data,
        ]);
    }
}
