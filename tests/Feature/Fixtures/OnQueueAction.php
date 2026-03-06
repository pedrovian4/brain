<?php

declare(strict_types=1);

namespace Tests\Feature\Fixtures;

use Brain\Action;
use Brain\Attributes\OnQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

#[OnQueue('custom')]
class OnQueueAction extends Action implements ShouldQueue
{
    public function handle(): self
    {
        return $this;
    }
}
