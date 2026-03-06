<?php

declare(strict_types=1);

namespace Tests\Feature\Fixtures;

use Brain\Action;
use Brain\Attributes\Sensitive;

/**
 * @property-read string $email
 * @property string $password
 * @property string $credit_card
 */
#[Sensitive('password', 'credit_card')]
class SensitiveUserAction extends Action
{
    public function handle(): self
    {
        return $this;
    }
}
