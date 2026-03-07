<?php

declare(strict_types=1);

namespace Brain\Actions\Middleware;

use Brain\Action;
use Brain\Actions\Events\Error as ActionsError;
use Illuminate\Support\Facades\Context;
use Throwable;

/** Middleware that finalizes an action after the pipeline completes. */
final class FinalizeActionMiddleware
{
    /**
     * Run the next middleware and then finalize the action.
     *
     * @throws Throwable
     */
    public function handle(Action $action, callable $next): void
    {
        [, $runWorkflowId] = Context::get('workflow');

        try {
            $next($action);

            $action->finalize();
            // @codeCoverageIgnoreStart
            // The coverage is ignored because the event doesn't dispatch event in the test environment
        } catch (Throwable $e) {
            $meta = [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ];

            event(new ActionsError($action::class, payload: $action->payload, runWorkflowId: $runWorkflowId, meta: $meta));

            $action->fail($e);

            throw $e;
            // @codeCoverageIgnoreEnd
        }
    }
}
