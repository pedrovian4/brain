<?php

declare(strict_types=1);

namespace Brain\Workflows\Listeners;

use Brain\Workflows\Events\BaseEvent;
use Illuminate\Support\Facades\Log;

/** Listener that logs workflow lifecycle events. */
class LogEventListener
{
    /**
     * Handle the event.
     */
    public function handle(BaseEvent $event): void
    {
        $class = $event::class;

        Log::info(
            "(id: {$event->runWorkflowId}) Workflow Event: {$class}",
            [
                'runId' => $event->runWorkflowId,
                'workflow' => $event->workflow,
                'payload' => $event->payload,
                'timestamp' => now()->toDateTimeString(),
                'meta' => $event->meta,
            ]
        );
    }
}
