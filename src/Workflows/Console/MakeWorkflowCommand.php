<?php

declare(strict_types=1);

namespace Brain\Workflows\Console;

use Brain\Console\BaseCommand;
use Override;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class MakeWorkflowCommand
 */
final class MakeWorkflowCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'brain:make:workflow';

    /**
     * The console command name aliases.
     *
     * @var array
     */
    protected $aliases = ['make:workflow'];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new workflow class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Workflow';

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        return __DIR__.'/stubs/workflow.stub';
    }

    /**
     * Get the name input for the class.
     *
     * @return string The name of the class
     */
    #[Override]
    protected function getNameInput(): string
    {
        $name = trim($this->argument('name'));

        if (config('brain.use_suffix', false) === false) {
            return $name;
        }

        $suffix = config('brain.suffixes.workflow');

        return str_ends_with($name, (string) $suffix) ? $name : "{$name}{$suffix}";
    }

    /**
     * Get the console command arguments required for this command.
     *
     * @return array<int, array<string, int, string>> An array of arguments with their details
     */
    #[Override]
    protected function getArguments(): array
    {
        $arguments = [
            ['name', InputArgument::REQUIRED, 'The name of the workflow'],
        ];

        if (config('brain.use_domains', false) === true) {
            $arguments[] = ['domain', InputArgument::OPTIONAL, 'The domain of the workflow'];
        }

        return $arguments;
    }
}
