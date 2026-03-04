<?php

declare(strict_types=1);

namespace Brain\Tasks\Events;

/**
 * Dispatched when a task encounters an error.
 *
 * @deprecated Use Brain\Actions\Events\Error instead.
 */
final class Error extends BaseEvent {}
