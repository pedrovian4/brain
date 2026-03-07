# Events & Logging

Brain dispatches events throughout the lifecycle of workflows and actions. These can be used for logging, monitoring, or triggering additional logic.

## Enabling Logging

```bash
BRAIN_LOG_ENABLED=true
```

Or in `config/brain.php`:

```php
'log' => env('BRAIN_LOG_ENABLED', true),
```

## Workflow Events

| Event | When |
|-------|------|
| `Brain\Workflows\Events\Processing` | Workflow starts executing |
| `Brain\Workflows\Events\Processed` | Workflow completes successfully |
| `Brain\Workflows\Events\Error` | Workflow encounters an error |

Each event contains:

- `process` — The workflow class name
- `runProcessId` — A unique ID for this execution
- `payload` — The data passed to the workflow
- `meta` — Additional metadata

## Action Events

| Event | When |
|-------|------|
| `Brain\Actions\Events\Processing` | Action starts executing |
| `Brain\Actions\Events\Processed` | Action completes successfully |
| `Brain\Actions\Events\Cancelled` | Action is cancelled via `cancelProcess()` |
| `Brain\Actions\Events\Skipped` | Action is skipped (`runIf()` returns false) |
| `Brain\Actions\Events\Error` | Action encounters an error |

Each event contains:

- `task` — The action class name
- `payload` — The data passed to the action
- `process` — The parent workflow class name (if applicable)
- `runProcessId` — The workflow execution ID (if applicable)
- `meta` — Additional metadata

## Custom Listeners

Register listeners in your `EventServiceProvider`:

```php
use Brain\Workflows\Events\Processed as WorkflowCompleted;
use Brain\Actions\Events\Error as ActionFailed;

protected $listen = [
    WorkflowCompleted::class => [
        NotifyAdmin::class,
    ],
    ActionFailed::class => [
        AlertOpsTeam::class,
    ],
];
```

Or use closures:

```php
use Brain\Actions\Events\Processed;

Event::listen(Processed::class, function ($event) {
    logger()->info('Action completed', [
        'task'    => $event->task,
        'process' => $event->process,
    ]);
});
```
