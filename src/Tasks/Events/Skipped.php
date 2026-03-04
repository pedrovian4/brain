<?php

declare(strict_types=1);

namespace Brain\Tasks\Events;

/**
 * Dispatched when a task has been skipped.
 *
 * @deprecated Use Brain\Actions\Events\Skipped instead.
 */
final class Skipped extends BaseEvent {}
