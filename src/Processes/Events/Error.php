<?php

declare(strict_types=1);

namespace Brain\Processes\Events;

/**
 * Dispatched when a process encounters an error.
 *
 * @deprecated Use Brain\Workflows\Events\Error instead.
 */
final class Error extends BaseEvent {}
