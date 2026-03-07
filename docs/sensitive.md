# Sensitive Data

Use the `#[Sensitive]` attribute to mark payload properties that should be automatically redacted in logs, JSON serialization, and debug output.

## Usage

```php
use Brain\Attributes\Sensitive;
use Brain\Action;

/**
 * @property-read string $email
 * @property string $password
 * @property string $credit_card
 */
#[Sensitive('password', 'credit_card')]
class CreateUser extends Action
{
    public function handle(): self
    {
        // $this->password returns the real value inside handle()
        // but logs, JSON, and debug output show "**********"
        return $this;
    }
}
```

## How It Works

Sensitive values are internally wrapped in a `SensitiveValue` object:

- **Inside the action** — `$this->password` returns the real value transparently
- **In logs** — Replaced with `**********`
- **In JSON** — Replaced with `**********`
- **In debug output** — Replaced with `**********`
- **In `brain:show -vv`** — Shows a `[sensitive]` indicator

## Workflow-Level Inheritance

When `#[Sensitive]` is applied to a Workflow, **all child actions** automatically inherit the sensitive keys — even if the actions themselves don't declare the attribute:

```php
use Brain\Attributes\Sensitive;
use Brain\Workflow;

#[Sensitive('password', 'credit_card')]
class CreateUser extends Workflow
{
    protected array $actions = [
        ValidateInput::class,     // password & credit_card redacted
        ChargeCustomer::class,    // password & credit_card redacted
        SendConfirmation::class,  // password & credit_card redacted
    ];
}
```

Action-level and workflow-level keys are merged and deduplicated. An action can define additional sensitive keys beyond what the workflow specifies:

```php
#[Sensitive('password')]
class CreateUser extends Workflow
{
    protected array $actions = [
        ChargeCustomer::class, // has #[Sensitive('cvv')] → both password and cvv are redacted
    ];
}
```

::: tip
The `brain:show -vv` command displays a `[sensitive]` indicator next to sensitive properties, making it easy to verify your configuration.
:::
