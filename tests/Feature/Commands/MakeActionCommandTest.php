<?php

declare(strict_types=1);

use Brain\Actions\Console\MakeActionCommand;
use Brain\Console\BaseCommand;
use Illuminate\Filesystem\Filesystem;
use Tests\Feature\Fixtures\TestInput;

test('extends BaseCommand', function (): void {
    $files = app(Filesystem::class);
    $command = new MakeActionCommand($files);

    expect($command)->toBeInstanceOf(BaseCommand::class);
});

test('name should be make:action', function (): void {
    $files = app(Filesystem::class);
    $command = new MakeActionCommand($files);

    expect($command->getName())->toBe('brain:make:action');
});

it('should have aliases for command signature', function (): void {
    $files = app(Filesystem::class);
    $command = new MakeActionCommand($files);

    expect($command->getAliases())->toBe(['make:action']);
});

test('description should be \'Create a new action class\'', function (): void {
    $files = app(Filesystem::class);
    $command = new MakeActionCommand($files);

    expect($command->getDescription())->toBe('Create a new action class');
});

test('stub should be __DIR__./stubs/action/stub', function (): void {
    $files = app(Filesystem::class);
    $command = new MakeActionCommand($files);

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('getStub');
    $stubPath = $method->invoke($command);

    $expectedPath = realpath(__DIR__.'/../../../src/Actions/Console/stubs/action.stub');
    $actualPath = realpath($stubPath);

    expect($actualPath)->toBe($expectedPath);
});

test('get defaultNamespace', function (): void {
    config(['brain.use_domains' => true]);

    $files = app(Filesystem::class);
    $command = new MakeActionCommand($files);

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('getDefaultNamespace');

    $input = new TestInput(['domain' => 'Domain']);
    $command->setInput($input);

    $defaultNamespace = str($method->invoke($command, 'App\\'))->replace('\\\\', '\\')->toString();

    expect($defaultNamespace)->toBe('App\Brain\Domain\Actions');
});

test('get defaultNamespace with no domain', function (): void {
    config(['brain.use_domains' => true]);
    $files = app(Filesystem::class);
    $command = new MakeActionCommand($files);

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('getDefaultNamespace');

    $input = new TestInput([]);
    $command->setInput($input);

    $defaultNamespace = str($method->invoke($command, 'App\\'))->replace('\\\\', '\\')->toString();

    expect($defaultNamespace)->toBe('App\Brain\TempDomain\Actions');
});

test('getNameInput should return the name as is when suffix is disabled', function (): void {
    $files = app(Filesystem::class);
    $command = new MakeActionCommand($files);
    $reflection = new ReflectionClass($command);

    config(['brain.use_suffix' => false]);
    $input = new TestInput(['name' => 'CreateTenant']);
    $command->setInput($input);

    $method = $reflection->getMethod('getNameInput');

    $nameInput = $method->invoke($command);

    expect($nameInput)->toBe('CreateTenant');
});

test('getNameInput should append Action when suffix is enabled', function (): void {
    $files = app(Filesystem::class);
    $command = new MakeActionCommand($files);
    $reflection = new ReflectionClass($command);

    config(['brain.use_suffix' => true]);
    $input = new TestInput(['name' => 'CreateTenant']);
    $command->setInput($input);

    $method = $reflection->getMethod('getNameInput');

    $nameInput = $method->invoke($command);

    expect($nameInput)->toBe('CreateTenantAction');
});

test('getNameInput should not duplicate the Action suffix', function (): void {
    $files = app(Filesystem::class);
    $command = new MakeActionCommand($files);
    $reflection = new ReflectionClass($command);

    config(['brain.use_suffix' => true]);
    $input = new TestInput(['name' => 'CreateTenantAction']);
    $command->setInput($input);

    $method = $reflection->getMethod('getNameInput');

    $nameInput = $method->invoke($command);
    expect($nameInput)->toBe('CreateTenantAction');
});

// ------------------------------------------------------------------------------------------------------
// Disabling Domains
test('get defaultNamespace with domains disabled', function (): void {
    config(['brain.use_domains' => false]);

    $files = app(Filesystem::class);
    $command = new MakeActionCommand($files);

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('getDefaultNamespace');

    $input = new TestInput;
    $command->setInput($input);

    $defaultNamespace = str($method->invoke($command, 'App\\'))->replace('\\\\', '\\')->toString();

    expect($defaultNamespace)->toBe('App\Brain\Actions');
});
