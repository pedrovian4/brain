<?php

declare(strict_types=1);

namespace Brain\Actions\Listeners;

use Brain\Actions\Events\BaseEvent;
use Illuminate\Support\Facades\Log;

/** Listener that logs action lifecycle events. */
class LogEventListener
{
    /**
     * Handle the event.
     */
    public function handle(BaseEvent $event): void
    {
        $class = $event::class;

        Log::info(
            "(id: {$event->runWorkflowId}) Action Event: {$class}",
            [
                'runId' => $event->runWorkflowId,
                'action' => $event->action,
                'payload' => $event->payload,
                'workflow' => $event->workflow,
                'timestamp' => now()->toDateTimeString(),
                'meta' => $event->meta,
            ]
        );
    }
}
