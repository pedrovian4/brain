# Queues

Brain integrates with Laravel's queue system. Actions and Workflows can be dispatched to the queue by implementing `ShouldQueue`.

## Queueable Actions

Implement `ShouldQueue` on your action:

```php
use Brain\Action;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendWelcomeEmail extends Action implements ShouldQueue
{
    public function handle(): self
    {
        // runs on the queue
        return $this;
    }
}
```

## Delayed Execution

Use the `runIn()` method to delay a queued action:

```php
class SendFollowUp extends Action implements ShouldQueue
{
    protected function runIn(): int|Carbon|null
    {
        return now()->addDays(2);
    }

    public function handle(): self
    {
        // ...
        return $this;
    }
}
```

## Setting a Queue with `#[OnQueue]`

Use the `#[OnQueue]` attribute to assign a specific queue:

```php
use Brain\Attributes\OnQueue;
use Brain\Action;
use Illuminate\Contracts\Queue\ShouldQueue;

#[OnQueue('emails')]
class SendWelcomeEmail extends Action implements ShouldQueue
{
    public function handle(): self
    {
        return $this;
    }
}
```

::: danger
Do **not** declare `public string $queue = 'my-queue'` on an Action — this causes a PHP fatal error because Laravel's `Queueable` trait already declares `$queue` without a type hint. Always use `#[OnQueue]` instead.
:::

## Workflow-Level Queue

When `#[OnQueue]` is applied to a Workflow:

1. The Workflow itself is dispatched to that queue (if it implements `ShouldQueue`)
2. All queued child actions **inherit** the Workflow queue — unless the action defines its own `#[OnQueue]`

```php
#[OnQueue('strava')]
class SyncActivities extends Workflow implements ShouldQueue
{
    protected array $actions = [
        FetchActivities::class,   // ShouldQueue → runs on "strava" (inherited)
        SaveActivities::class,    // sync action → unaffected
        NotifyUser::class,        // #[OnQueue('emails')] → runs on "emails"
    ];
}
```

::: tip
An action's own `#[OnQueue]` always takes precedence over the workflow-level queue.
:::

## Execution Patterns

### All actions on the same queue

The workflow and every queued action run on `strava`:

```mermaid
flowchart LR
    D((Dispatch)) --> Q[strava queue]

    subgraph Q[strava queue]
        direction LR
        P[SyncActivities] --> T1[FetchActivities]
        T1 --> T2[TransformData]
        T2 --> T3[SaveActivities]
    end
```

### Workflow queued, actions synchronous

The workflow is queued, but actions run synchronously inside the job:

```mermaid
flowchart LR
    D((Dispatch)) --> Q

    subgraph Q[strava queue]
        subgraph P[SyncActivities]
            direction LR
            T1[FetchActivities] --> T2[TransformData] --> T3[SaveActivities]
        end
    end
```

### Mixed sync and queued

Some actions run inside the workflow, others are dispatched to queues:

```mermaid
flowchart LR
    D((Dispatch)) --> Q

    subgraph Q[strava queue]
        subgraph P[SyncActivities]
            direction LR
            T1[FetchActivities] --> T2[TransformData]
        end
        T2 --> T3[SaveActivities]
    end

    T3 -.-> E[emails queue]

    subgraph E[emails queue]
        T4[NotifyUser]
    end
```
