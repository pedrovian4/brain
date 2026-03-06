<?php

declare(strict_types=1);

namespace Tests\Feature\Fixtures;

use Brain\Action;
use Illuminate\Contracts\Queue\ShouldQueue;

class QueuedAction extends Action implements ShouldQueue
{
    public function handle(): self
    {
        return $this;
    }
}
