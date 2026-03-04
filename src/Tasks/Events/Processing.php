<?php

declare(strict_types=1);

namespace Brain\Tasks\Events;

/**
 * Dispatched when a task is about to be processed.
 *
 * @deprecated Use Brain\Actions\Events\Processing instead.
 */
final class Processing extends BaseEvent {}
