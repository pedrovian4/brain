<?php

declare(strict_types=1);

namespace Brain\Tasks\Events;

/**
 * Dispatched when a task has been cancelled.
 *
 * @deprecated Use Brain\Actions\Events\Cancelled instead.
 */
final class Cancelled extends BaseEvent {}
