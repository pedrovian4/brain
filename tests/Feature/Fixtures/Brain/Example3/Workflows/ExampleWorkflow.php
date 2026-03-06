<?php

declare(strict_types=1);

namespace Tests\Feature\Fixtures\Brain\Example3\Workflows;

use Brain\Workflow;
use Tests\Feature\Fixtures\Brain\Example3\Actions\ExampleAction;

class ExampleWorkflow extends Workflow
{
    protected array $actions = [
        ExampleAction::class,
    ];
}
