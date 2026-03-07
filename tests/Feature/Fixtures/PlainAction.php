<?php

declare(strict_types=1);

namespace Tests\Feature\Fixtures;

use Brain\Action;

/**
 * @property string $password
 * @property string $email
 */
class PlainAction extends Action
{
    public function handle(): self
    {
        return $this;
    }
}
