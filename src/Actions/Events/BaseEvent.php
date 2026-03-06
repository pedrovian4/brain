<?php

declare(strict_types=1);

namespace Brain\Actions\Events;

use Illuminate\Foundation\Events\Dispatchable;

/** Base event for all action-related events. */
class BaseEvent
{
    use Dispatchable;

    /** Create a new action event instance. */
    public function __construct(
        /** The action identifier. */
        public string $action,
        /** The event payload data. */
        public array|object|null $payload,
        /** The parent workflow identifier. */
        public ?string $workflow = null,
        /** The run workflow identifier. */
        public ?string $runWorkflowId = null,
        /** Additional metadata for the event. */
        public array $meta = []
    ) {}
}
