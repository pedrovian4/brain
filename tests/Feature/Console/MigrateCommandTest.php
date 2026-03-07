<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

beforeEach(function (): void {
    $this->brainBase = app_path('Brain');

    if (File::isDirectory($this->brainBase)) {
        File::deleteDirectory($this->brainBase);
    }
});

afterEach(function (): void {
    if (File::isDirectory($this->brainBase)) {
        File::deleteDirectory($this->brainBase);
    }
});

test('it fails when brain directory does not exist', function (): void {
    $this->artisan('brain:migrate')
        ->assertExitCode(1);
});

test('it reports nothing to migrate when already using new naming', function (): void {
    File::ensureDirectoryExists($this->brainBase.'/Workflows');
    File::put($this->brainBase.'/Workflows/CreateOrder.php', <<<'PHP'
<?php

namespace App\Brain\Workflows;

use Brain\Workflow;

class CreateOrder extends Workflow
{
    protected array $actions = [
        //
    ];
}
PHP);

    $this->artisan('brain:migrate')
        ->expectsOutputToContain('Nothing to migrate')
        ->assertExitCode(0);
});

test('it previews changes in dry-run mode', function (): void {
    File::ensureDirectoryExists($this->brainBase.'/Processes');
    File::put($this->brainBase.'/Processes/CreateOrder.php', <<<'PHP'
<?php

namespace App\Brain\Processes;

use Brain\Process;

class CreateOrder extends Process
{
    protected array $tasks = [
        //
    ];
}
PHP);

    $this->artisan('brain:migrate', ['--dry-run' => true])
        ->expectsOutputToContain('Dry-run complete')
        ->assertExitCode(0);

    // File should NOT have been modified
    $content = File::get($this->brainBase.'/Processes/CreateOrder.php');
    expect($content)->toContain('use Brain\\Process;');
    expect($content)->toContain('extends Process');

    // Directory should NOT have been renamed
    expect(File::isDirectory($this->brainBase.'/Processes'))->toBeTrue();
    expect(File::isDirectory($this->brainBase.'/Workflows'))->toBeFalse();
});

test('it migrates Process to Workflow', function (): void {
    File::ensureDirectoryExists($this->brainBase.'/Processes');
    File::put($this->brainBase.'/Processes/CreateOrder.php', <<<'PHP'
<?php

namespace App\Brain\Processes;

use Brain\Process;

class CreateOrder extends Process
{
    protected array $tasks = [
        //
    ];
}
PHP);

    $this->artisan('brain:migrate')
        ->expectsConfirmation('Apply these changes?', 'yes')
        ->assertExitCode(0);

    // Directory should have been renamed
    expect(File::isDirectory($this->brainBase.'/Processes'))->toBeFalse();
    expect(File::isDirectory($this->brainBase.'/Workflows'))->toBeTrue();

    // File content should have been updated
    $content = File::get($this->brainBase.'/Workflows/CreateOrder.php');
    expect($content)->toContain('namespace App\\Brain\\Workflows;');
    expect($content)->toContain('use Brain\\Workflow;');
    expect($content)->toContain('extends Workflow');
    expect($content)->toContain('protected array $actions = [');
    expect($content)->not->toContain('use Brain\\Process;');
    expect($content)->not->toContain('extends Process');
    expect($content)->not->toContain('protected array $tasks = [');
});

test('it migrates Task to Action', function (): void {
    File::ensureDirectoryExists($this->brainBase.'/Tasks');
    File::put($this->brainBase.'/Tasks/ChargeUser.php', <<<'PHP'
<?php

namespace App\Brain\Tasks;

use Brain\Task;

/**
 * @property-read string $userId
 */
class ChargeUser extends Task
{
    public function handle(): self
    {
        return $this;
    }
}
PHP);

    $this->artisan('brain:migrate')
        ->expectsConfirmation('Apply these changes?', 'yes')
        ->assertExitCode(0);

    expect(File::isDirectory($this->brainBase.'/Tasks'))->toBeFalse();
    expect(File::isDirectory($this->brainBase.'/Actions'))->toBeTrue();

    $content = File::get($this->brainBase.'/Actions/ChargeUser.php');
    expect($content)->toContain('namespace App\\Brain\\Actions;');
    expect($content)->toContain('use Brain\\Action;');
    expect($content)->toContain('extends Action');
    expect($content)->not->toContain('use Brain\\Task;');
    expect($content)->not->toContain('extends Task');
});

test('it migrates both Processes and Tasks together', function (): void {
    File::ensureDirectoryExists($this->brainBase.'/Processes');
    File::ensureDirectoryExists($this->brainBase.'/Tasks');

    File::put($this->brainBase.'/Processes/CreateOrder.php', <<<'PHP'
<?php

namespace App\Brain\Processes;

use Brain\Process;

class CreateOrder extends Process
{
    protected array $tasks = [
        //
    ];
}
PHP);

    File::put($this->brainBase.'/Tasks/ChargeUser.php', <<<'PHP'
<?php

namespace App\Brain\Tasks;

use Brain\Task;

class ChargeUser extends Task
{
    public function handle(): self
    {
        return $this;
    }
}
PHP);

    $this->artisan('brain:migrate')
        ->expectsConfirmation('Apply these changes?', 'yes')
        ->assertExitCode(0);

    expect(File::isDirectory($this->brainBase.'/Workflows'))->toBeTrue();
    expect(File::isDirectory($this->brainBase.'/Actions'))->toBeTrue();
    expect(File::isDirectory($this->brainBase.'/Processes'))->toBeFalse();
    expect(File::isDirectory($this->brainBase.'/Tasks'))->toBeFalse();
});

test('it handles domain-based structure', function (): void {
    File::ensureDirectoryExists($this->brainBase.'/Orders/Processes');
    File::ensureDirectoryExists($this->brainBase.'/Orders/Tasks');

    File::put($this->brainBase.'/Orders/Processes/CreateOrder.php', <<<'PHP'
<?php

namespace App\Brain\Orders\Processes;

use Brain\Process;

class CreateOrder extends Process
{
    protected array $tasks = [
        //
    ];
}
PHP);

    File::put($this->brainBase.'/Orders/Tasks/ChargeUser.php', <<<'PHP'
<?php

namespace App\Brain\Orders\Tasks;

use Brain\Task;

class ChargeUser extends Task
{
    public function handle(): self
    {
        return $this;
    }
}
PHP);

    $this->artisan('brain:migrate')
        ->expectsConfirmation('Apply these changes?', 'yes')
        ->assertExitCode(0);

    expect(File::isDirectory($this->brainBase.'/Orders/Workflows'))->toBeTrue();
    expect(File::isDirectory($this->brainBase.'/Orders/Actions'))->toBeTrue();
    expect(File::isDirectory($this->brainBase.'/Orders/Processes'))->toBeFalse();
    expect(File::isDirectory($this->brainBase.'/Orders/Tasks'))->toBeFalse();

    $content = File::get($this->brainBase.'/Orders/Workflows/CreateOrder.php');
    expect($content)->toContain('namespace App\\Brain\\Orders\\Workflows;');
    expect($content)->toContain('use Brain\\Workflow;');
});

test('it can be cancelled by the user', function (): void {
    File::ensureDirectoryExists($this->brainBase.'/Processes');
    File::put($this->brainBase.'/Processes/CreateOrder.php', <<<'PHP'
<?php

namespace App\Brain\Processes;

use Brain\Process;

class CreateOrder extends Process
{
    protected array $tasks = [
        //
    ];
}
PHP);

    $this->artisan('brain:migrate')
        ->expectsConfirmation('Apply these changes?', 'no')
        ->expectsOutputToContain('cancelled')
        ->assertExitCode(0);

    // Nothing should have changed
    expect(File::isDirectory($this->brainBase.'/Processes'))->toBeTrue();
    $content = File::get($this->brainBase.'/Processes/CreateOrder.php');
    expect($content)->toContain('use Brain\\Process;');
});

test('it skips directories that already exist at target', function (): void {
    File::ensureDirectoryExists($this->brainBase.'/Processes');
    File::ensureDirectoryExists($this->brainBase.'/Workflows');

    File::put($this->brainBase.'/Processes/CreateOrder.php', <<<'PHP'
<?php

namespace App\Brain\Processes;

use Brain\Process;

class CreateOrder extends Process
{
    protected array $tasks = [
        //
    ];
}
PHP);

    $this->artisan('brain:migrate')
        ->expectsConfirmation('Apply these changes?', 'yes')
        ->assertExitCode(0);

    // File contents should still be updated even if dir can't be renamed
    $content = File::get($this->brainBase.'/Processes/CreateOrder.php');
    expect($content)->toContain('use Brain\\Workflow;');
    expect($content)->toContain('extends Workflow');

    // Old dir should still exist since target already existed
    expect(File::isDirectory($this->brainBase.'/Processes'))->toBeTrue();
});

test('it ignores non-php files', function (): void {
    File::ensureDirectoryExists($this->brainBase.'/Workflows');
    File::put($this->brainBase.'/Workflows/notes.txt', 'extends Process');

    $this->artisan('brain:migrate')
        ->expectsOutputToContain('Nothing to migrate')
        ->assertExitCode(0);

    // Text file should not have been modified
    expect(File::get($this->brainBase.'/Workflows/notes.txt'))->toBe('extends Process');
});

test('it renames files with Process suffix to Workflow', function (): void {
    File::ensureDirectoryExists($this->brainBase.'/Processes');
    File::put($this->brainBase.'/Processes/CreateOrderProcess.php', <<<'PHP'
<?php

namespace App\Brain\Processes;

use Brain\Process;

class CreateOrderProcess extends Process
{
    protected array $tasks = [
        //
    ];
}
PHP);

    $this->artisan('brain:migrate')
        ->expectsConfirmation('Apply these changes?', 'yes')
        ->assertExitCode(0);

    expect(File::exists($this->brainBase.'/Workflows/CreateOrderWorkflow.php'))->toBeTrue();
    expect(File::exists($this->brainBase.'/Workflows/CreateOrderProcess.php'))->toBeFalse();

    $content = File::get($this->brainBase.'/Workflows/CreateOrderWorkflow.php');
    expect($content)->toContain('class CreateOrderWorkflow extends Workflow');
    expect($content)->not->toContain('CreateOrderProcess');
});

test('it renames files with Task suffix to Action', function (): void {
    File::ensureDirectoryExists($this->brainBase.'/Tasks');
    File::put($this->brainBase.'/Tasks/ChargeUserTask.php', <<<'PHP'
<?php

namespace App\Brain\Tasks;

use Brain\Task;

class ChargeUserTask extends Task
{
    public function handle(): self
    {
        return $this;
    }
}
PHP);

    $this->artisan('brain:migrate')
        ->expectsConfirmation('Apply these changes?', 'yes')
        ->assertExitCode(0);

    expect(File::exists($this->brainBase.'/Actions/ChargeUserAction.php'))->toBeTrue();
    expect(File::exists($this->brainBase.'/Actions/ChargeUserTask.php'))->toBeFalse();

    $content = File::get($this->brainBase.'/Actions/ChargeUserAction.php');
    expect($content)->toContain('class ChargeUserAction extends Action');
    expect($content)->not->toContain('ChargeUserTask');
});

test('it updates class references across files when renaming suffixed classes', function (): void {
    File::ensureDirectoryExists($this->brainBase.'/Processes');
    File::ensureDirectoryExists($this->brainBase.'/Tasks');

    File::put($this->brainBase.'/Tasks/ChargeUserTask.php', <<<'PHP'
<?php

namespace App\Brain\Tasks;

use Brain\Task;

class ChargeUserTask extends Task
{
    public function handle(): self
    {
        return $this;
    }
}
PHP);

    File::put($this->brainBase.'/Processes/CreateOrderProcess.php', <<<'PHP'
<?php

namespace App\Brain\Processes;

use App\Brain\Tasks\ChargeUserTask;
use Brain\Process;

class CreateOrderProcess extends Process
{
    protected array $tasks = [
        ChargeUserTask::class,
    ];
}
PHP);

    $this->artisan('brain:migrate')
        ->expectsConfirmation('Apply these changes?', 'yes')
        ->assertExitCode(0);

    $content = File::get($this->brainBase.'/Workflows/CreateOrderWorkflow.php');
    expect($content)->toContain('ChargeUserAction::class');
    expect($content)->not->toContain('ChargeUserTask');
    expect($content)->toContain('CreateOrderWorkflow extends Workflow');
});

test('it does not rename files when target already exists', function (): void {
    File::ensureDirectoryExists($this->brainBase.'/Tasks');
    File::put($this->brainBase.'/Tasks/ChargeUserTask.php', <<<'PHP'
<?php

namespace App\Brain\Tasks;

use Brain\Task;

class ChargeUserTask extends Task
{
    public function handle(): self
    {
        return $this;
    }
}
PHP);

    // Pre-create the target file
    File::put($this->brainBase.'/Tasks/ChargeUserAction.php', '<?php // existing');

    $this->artisan('brain:migrate')
        ->expectsConfirmation('Apply these changes?', 'yes')
        ->assertExitCode(0);

    // Original file should still exist (not renamed since target exists)
    expect(File::exists($this->brainBase.'/Actions/ChargeUserTask.php'))->toBeTrue();
});

test('it handles case-insensitive extends and use statements', function (): void {
    File::ensureDirectoryExists($this->brainBase.'/Tasks');
    File::put($this->brainBase.'/Tasks/StepOne.php', <<<'PHP'
<?php

namespace App\Brain\Tasks;

use Brain\task;

class stepone extends task
{
    public function handle(): self
    {
        return $this;
    }
}
PHP);

    $this->artisan('brain:migrate')
        ->expectsConfirmation('Apply these changes?', 'yes')
        ->assertExitCode(0);

    $content = File::get($this->brainBase.'/Actions/StepOne.php');
    expect($content)->toContain('use Brain\\Action;');
    expect($content)->toContain('extends Action');
    expect($content)->not->toContain('use Brain\\task;');
    expect($content)->not->toContain('extends task');
});

test('it works with null root config (flat structure)', function (): void {
    config()->set('brain.root', null);

    $appBase = app_path();

    // Clean any leftover flat-structure directories
    File::deleteDirectory($appBase.'/Tasks');
    File::deleteDirectory($appBase.'/Actions');

    File::ensureDirectoryExists($appBase.'/Tasks');
    File::put($appBase.'/Tasks/ChargeUser.php', <<<'PHP'
<?php

namespace App\Tasks;

use Brain\Task;

class ChargeUser extends Task
{
    public function handle(): self
    {
        return $this;
    }
}
PHP);

    $this->artisan('brain:migrate')
        ->expectsConfirmation('Apply these changes?', 'yes')
        ->assertExitCode(0);

    expect(File::isDirectory($appBase.'/Actions'))->toBeTrue();

    $content = File::get($appBase.'/Actions/ChargeUser.php');
    expect($content)->toContain('use Brain\\Action;');
    expect($content)->toContain('extends Action');

    // Cleanup
    File::deleteDirectory($appBase.'/Actions');
    File::deleteDirectory($appBase.'/Tasks');
});
