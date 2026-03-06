<?php

declare(strict_types=1);

test('config keys brain.suffixes.workflow and brain.suffixes.action exist', function (): void {
    expect(config('brain.suffixes.workflow'))->toBe('Workflow');
    expect(config('brain.suffixes.action'))->toBe('Action');
});

test('deprecated config keys brain.suffixes.process and brain.suffixes.task still exist', function (): void {
    expect(config('brain.suffixes.process'))->toBe('Process');
    expect(config('brain.suffixes.task'))->toBe('Task');
});

test('brain:make:workflow command exists', function (): void {
    $this->artisan('brain:make:workflow', ['name' => 'TestWorkflow', '--dry-run' => true])
        ->assertSuccessful();
})->skip(fn (): true => true, 'dry-run not supported, tested via MakeWorkflowCommandTest');

test('brain:make:action command exists', function (): void {
    $this->artisan('brain:make:action', ['name' => 'TestAction', '--dry-run' => true])
        ->assertSuccessful();
})->skip(fn (): true => true, 'dry-run not supported, tested via MakeActionCommandTest');

test('deprecated brain:make:process command still exists', function (): void {
    $commands = Artisan::all();

    expect($commands)->toHaveKey('brain:make:process');
});

test('deprecated brain:make:task command still exists', function (): void {
    $commands = Artisan::all();

    expect($commands)->toHaveKey('brain:make:task');
});

test('new brain:make:workflow command is registered', function (): void {
    $commands = Artisan::all();

    expect($commands)->toHaveKey('brain:make:workflow');
});

test('new brain:make:action command is registered', function (): void {
    $commands = Artisan::all();

    expect($commands)->toHaveKey('brain:make:action');
});

test('brain:show has --workflows flag', function (): void {
    $command = Artisan::all()['brain:show'];
    $definition = $command->getDefinition();

    expect($definition->hasOption('workflows'))->toBeTrue();
});

test('brain:show has --actions flag', function (): void {
    $command = Artisan::all()['brain:show'];
    $definition = $command->getDefinition();

    expect($definition->hasOption('actions'))->toBeTrue();
});

test('brain:show still has deprecated --processes flag', function (): void {
    $command = Artisan::all()['brain:show'];
    $definition = $command->getDefinition();

    expect($definition->hasOption('processes'))->toBeTrue();
});

test('brain:show still has deprecated --tasks flag', function (): void {
    $command = Artisan::all()['brain:show'];
    $definition = $command->getDefinition();

    expect($definition->hasOption('tasks'))->toBeTrue();
});
