<?php

declare(strict_types=1);

namespace Brain\Workflows\Events;

use Illuminate\Foundation\Events\Dispatchable;

/** Base event for all workflow-related events. */
class BaseEvent
{
    use Dispatchable;

    /** Create a new workflow event instance. */
    public function __construct(
        /** The workflow identifier. */
        public string $workflow,
        /** The run workflow identifier. */
        public string $runWorkflowId,
        /** The event payload data. */
        public array|object $payload,
        /** Additional metadata for the event. */
        public array $meta = []
    ) {}
}
