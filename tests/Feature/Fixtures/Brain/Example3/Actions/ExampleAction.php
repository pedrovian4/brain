<?php

declare(strict_types=1);

namespace Tests\Feature\Fixtures\Brain\Example3\Actions;

use Brain\Action;

/**
 * Action ExampleAction
 *
 * @property-read string $email
 * @property-read int $paymentId
 */
class ExampleAction extends Action
{
    public function handle(): self
    {
        //

        return $this;
    }
}
