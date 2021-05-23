<?php

namespace OriginEngine\Pipeline\Tasks;

use Illuminate\Support\Collection;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;
use OriginEngine\Pipeline\Task;
use OriginEngine\Helpers\Terminal\Executor;
use OriginEngine\Pipeline\TaskResponse;

class BringSailEnvironmentUp extends Task
{

    public function __construct(bool $removeContainersOnDown = true)
    {
        parent::__construct([
            'removeContainersOnDown' => $removeContainersOnDown
        ]);
    }

    protected function execute(WorkingDirectory $workingDirectory, Collection $config): TaskResponse
    {
        Executor::cd($workingDirectory)
            ->execute('./vendor/bin/sail up -d');

        return $this->succeeded();
    }

    protected function undo(WorkingDirectory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        $command = './vendor/bin/sail down';
        if ($config->get('removeContainersOnDown', true)) {
            $command .= ' -v';
        }
        Executor::cd($workingDirectory)
            ->execute($command);
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
