# Commands

Brain provides several Artisan commands for scaffolding and inspecting your architecture.

## Scaffolding

### `make:workflow`

Create a new Workflow class:

```bash
php artisan make:workflow CreateUser
```

With domains enabled, you'll be prompted for a domain name.

### `make:action`

Create a new Action class:

```bash
php artisan make:action RegisterUser
```

### `make:query`

Create a new Query class:

```bash
php artisan make:query GetUserByEmail
```

You can optionally specify a model when prompted.

## Visualization

### `brain:show`

Visualize your entire Brain structure in the terminal:

```bash
php artisan brain:show
```

```
WKFL    CreateUserWorkflow ······································
WKFL    PaymentSucceededWorkflow ······················· chained
ACTN    NotifyStaffAction ································ queued
ACTN    RegisterUserAction ·····································
ACTN    SendWelcomeEmailAction ·································
QERY    GetPaymentQuery ········································
QERY    GetUserByEmailQuery ····································
```

### Filtering by Type

```bash
php artisan brain:show -w              # only workflows
php artisan brain:show -a              # only actions
php artisan brain:show -Q              # only queries
php artisan brain:show -w -a           # workflows and actions
```

### Filtering by Name

```bash
php artisan brain:show --filter=User
php artisan brain:show -w --filter=Payment
```

When combining `-w` with `--filter`, matching sub-action names show the parent workflow with only the matching sub-actions. Matching workflow names show all sub-actions.

### Verbosity

```bash
php artisan brain:show -v              # show sub-actions inside workflows
php artisan brain:show -vv             # also show action properties
```

```
WKFL    PaymentSucceededWorkflow ······················· chained
        ├── 1. A SavePaymentAction ······················ queued
        └── 2. A InviteUserAction ······························
ACTN    CreateCommentAction ····································
           → user_id: int
           ← comment: \Comment|null
QERY    ExampleQuery ···········································
```

- `→` — input property (`@property-read`)
- `←` — output property (`@property`)

## Execution

### `brain:run`

Interactively execute a Workflow or Action from the terminal:

```bash
php artisan brain:run
```

The command guides you through:

1. **Select a target** — Search and pick any Workflow or Action
2. **Choose dispatch mode** — Sync or Async
3. **Fill payload** — Enter values for required properties
4. **Preview** — Review before executing
5. **Execute** — Run and see the result

### Rerunning Previous Executions

Every successful run is saved to history. Use `--rerun` to replay:

```bash
php artisan brain:run --rerun
```

Search through past runs, preview the saved payload, and re-execute.

## Migration

### `brain:migrate`

Automatically migrate your codebase from Process/Task naming to Workflow/Action naming:

```bash
php artisan brain:migrate
```

The command scans your Brain directory and applies the following changes:

- **Imports** — `use Brain\Process` → `use Brain\Workflow`, `use Brain\Task` → `use Brain\Action`
- **Class inheritance** — `extends Process` → `extends Workflow`, `extends Task` → `extends Action`
- **Task arrays** — `protected array $tasks = [` → `protected array $actions = [`
- **Namespaces** — `\Processes\` → `\Workflows\`, `\Tasks\` → `\Actions\`
- **File suffixes** — `CreateOrderProcess.php` → `CreateOrderWorkflow.php`, `ChargeUserTask.php` → `ChargeUserAction.php`
- **Class references** — Updates all `::class` references across files
- **Directories** — Renames `Processes/` → `Workflows/`, `Tasks/` → `Actions/`

All replacements are case-insensitive, so `extends task` and `extends Task` are both handled.

### Preview Mode

Use `--dry-run` to preview all planned changes without applying them:

```bash
php artisan brain:migrate --dry-run
```

```
 Brain Migration: Process/Task → Workflow/Action
 ─────────────────────────────────────────────────
 Directory: /app/Brain
 Mode: dry-run (no changes will be made)

 Files to update:
   • Processes/CreateOrder.php
   • Tasks/ChargeUser.php

 Files to rename:
   • CreateOrderProcess.php → CreateOrderWorkflow.php

 Directories to rename:
   • Processes → Workflows
   • Tasks → Actions

 Dry-run complete. No changes were made.
```

The command works with all configuration modes: root directory, flat structure (`root: null`), and domain-based organization.

## Deprecated Commands

The following commands still work but are deprecated in favor of the new naming:

| Deprecated | Use Instead |
|-----------|-------------|
| `make:process` | `make:workflow` |
| `make:task` | `make:action` |
