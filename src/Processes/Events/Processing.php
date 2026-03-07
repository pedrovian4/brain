<?php

declare(strict_types=1);

namespace Brain\Processes\Events;

/**
 * Dispatched when a process is about to be processed.
 *
 * @deprecated Use Brain\Workflows\Events\Processing instead.
 */
final class Processing extends BaseEvent {}
