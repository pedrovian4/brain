<?php

declare(strict_types=1);

namespace Brain\Tasks\Events;

/**
 * Dispatched when a task has been successfully processed.
 *
 * @deprecated Use Brain\Actions\Events\Processed instead.
 */
final class Processed extends BaseEvent {}
