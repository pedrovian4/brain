<?php

declare(strict_types=1);

namespace Brain\Processes\Events;

/**
 * Dispatched when a process has been successfully processed.
 *
 * @deprecated Use Brain\Workflows\Events\Processed instead.
 */
final class Processed extends BaseEvent {}
