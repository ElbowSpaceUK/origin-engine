<?php

namespace OriginEngine\Pipeline\Tasks\LaravelSail;

use Illuminate\Support\Collection;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Task;
use OriginEngine\Helpers\Terminal\Executor;
use OriginEngine\Pipeline\TaskResponse;

class BringSailEnvironmentUp extends Task
{

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        $output = Executor::cd($workingDirectory)
            ->execute('./vendor/bin/sail up -d');

        $this->writeSuccess('Ran sail up');
        $this->writeDebug('Sail up output: ' . $output);

        return $this->succeeded([
            'output' => $output
        ]);
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        Executor::cd($workingDirectory)
            ->execute('./vendor/bin/sail down -v');
    }

    protected function upName(Collection $config): string
    {
        return 'Bringing environment up';
    }

    protected function downName(Collection $config): string
    {
        return 'Bringing environment down';
    }
}
