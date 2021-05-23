<?php

namespace OriginEngine\Pipeline\Tasks\LaravelSail;

use Illuminate\Support\Collection;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;
use OriginEngine\Pipeline\Task;
use OriginEngine\Helpers\Terminal\Executor;
use OriginEngine\Pipeline\TaskResponse;

class BringSailEnvironmentDown extends Task
{

    public function __construct(bool $removeContainers = true)
    {
        parent::__construct([
            'removeContainers' => $removeContainers
        ]);
    }

    protected function execute(WorkingDirectory $workingDirectory, Collection $config): TaskResponse
    {
        $command = './vendor/bin/sail down';

        if($config->get('removeContainers', false) === true) {
            $command .= ' -v';
        }

        $output = Executor::cd($workingDirectory)
            ->execute($command);

        $this->export('output', $output);

        $this->writeSuccess('Ran sail down');
        $this->writeDebug('Sail down output: ' . $output);

        return $this->succeeded();
    }

    protected function undo(WorkingDirectory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        Executor::cd($workingDirectory)
            ->execute('./vendor/bin/sail up -d');
    }

    protected function upName(Collection $config): string
    {
        return 'Bringing environment down';
    }

    protected function downName(Collection $config): string
    {
        return 'Bringing environment back up';
    }
}
