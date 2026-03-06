<?php

declare(strict_types=1);

use Brain\Console\BaseCommand;
use Brain\Workflows\Console\MakeWorkflowCommand;
use Illuminate\Filesystem\Filesystem;
use Tests\Feature\Fixtures\TestInput;

beforeEach(function (): void {
    config()->set('brain.use_domains', true);
});

test('extends BaseCommand', function (): void {
    $files = app(Filesystem::class);
    $command = new MakeWorkflowCommand($files);

    expect($command)->toBeInstanceOf(BaseCommand::class);
});

test('name should be make:workflow', function (): void {
    $files = app(Filesystem::class);
    $command = new MakeWorkflowCommand($files);

    expect($command->getName())->toBe('brain:make:workflow');
});

it('should have aliases for command signature', function (): void {
    $files = app(Filesystem::class);
    $command = new MakeWorkflowCommand($files);

    expect($command->getAliases())->toBe(['make:workflow']);
});

test('description should be \'Create a new workflow class\'', function (): void {
    $files = app(Filesystem::class);
    $command = new MakeWorkflowCommand($files);

    expect($command->getDescription())->toBe('Create a new workflow class');
});

test('stub should be __DIR__./stubs/workflow/stub', function (): void {
    $files = app(Filesystem::class);
    $command = new MakeWorkflowCommand($files);

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('getStub');
    $stubPath = $method->invoke($command);

    $expectedPath = realpath(__DIR__.'/../../../src/Workflows/Console/stubs/workflow.stub');
    $actualPath = realpath($stubPath);

    expect($actualPath)->toBe($expectedPath);
});

test('get defaultNamespace', function (): void {
    $files = app(Filesystem::class);
    $command = new MakeWorkflowCommand($files);

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('getDefaultNamespace');

    $input = new TestInput(['domain' => 'Domain']);
    $command->setInput($input);

    $defaultNamespace = str($method->invoke($command, 'App\\'))->replace('\\\\', '\\')->toString();
    expect($defaultNamespace)->toBe('App\Brain\Domain\Workflows');
});

test('get defaultNamespace with no domain', function (): void {
    $files = app(Filesystem::class);
    $command = new MakeWorkflowCommand($files);

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('getDefaultNamespace');

    $input = new TestInput([]);
    $command->setInput($input);

    $defaultNamespace = str($method->invoke($command, 'App\\'))->replace('\\\\', '\\')->toString();

    expect($defaultNamespace)->toBe('App\Brain\TempDomain\Workflows');
});

test('getNameInput should return the name as is when suffix is disabled', function (): void {
    $files = app(Filesystem::class);
    $command = new MakeWorkflowCommand($files);
    $reflection = new ReflectionClass($command);

    config(['brain.use_suffix' => false]);
    $input = new TestInput(['name' => 'CreateUser']);
    $command->setInput($input);

    $method = $reflection->getMethod('getNameInput');

    $nameInput = $method->invoke($command);

    expect($nameInput)->toBe('CreateUser');
});

test('getNameInput should append Workflow when suffix is enabled', function (): void {
    $files = app(Filesystem::class);
    $command = new MakeWorkflowCommand($files);
    $reflection = new ReflectionClass($command);

    config(['brain.use_suffix' => true]);
    $input = new TestInput(['name' => 'CreateUser']);
    $command->setInput($input);

    $method = $reflection->getMethod('getNameInput');

    $nameInput = $method->invoke($command);

    expect($nameInput)->toBe('CreateUserWorkflow');
});

test('getNameInput should not duplicate the Workflow suffix', function (): void {
    $files = app(Filesystem::class);
    $command = new MakeWorkflowCommand($files);
    $reflection = new ReflectionClass($command);

    config(['brain.use_suffix' => true]);
    $input = new TestInput(['name' => 'CreateUserWorkflow']);
    $command->setInput($input);

    $method = $reflection->getMethod('getNameInput');

    $nameInput = $method->invoke($command);
    expect($nameInput)->toBe('CreateUserWorkflow');
});

// ------------------------------------------------------------------------------------------------------
// Disabling Domains

test('get defaultNamespace without domains', function (): void {
    config(['brain.use_domains' => false]);

    $files = app(Filesystem::class);
    $command = new MakeWorkflowCommand($files);

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('getDefaultNamespace');

    $input = new TestInput;
    $command->setInput($input);

    $defaultNamespace = str($method->invoke($command, 'App\\'))->replace('\\\\', '\\')->toString();

    expect($defaultNamespace)->toBe('App\Brain\Workflows');
});

// ------------------------------------------------------------------------------------------------------
// Flat Structure

test('get defaultNamespace with flat structure', function (): void {
    config(['brain.root' => null]);
    config(['brain.use_domains' => false]);

    $files = app(Filesystem::class);
    $command = new MakeWorkflowCommand($files);

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('getDefaultNamespace');

    $input = new TestInput;
    $command->setInput($input);

    $defaultNamespace = str($method->invoke($command, 'App\\'))->replace('\\\\', '\\')->toString();

    expect($defaultNamespace)->toBe('App\Workflows');
});
