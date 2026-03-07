<?php

declare(strict_types=1);

namespace Tests\Feature\Fixtures;

use Brain\Attributes\Sensitive;
use Brain\Workflow;

#[Sensitive('password', 'credit_card')]
class SensitiveWorkflow extends Workflow
{
    protected array $actions = [
        PlainAction::class,
    ];
}
