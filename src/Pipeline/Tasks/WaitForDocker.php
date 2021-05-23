<?php

namespace OriginEngine\Pipeline\Tasks;

use Illuminate\Support\Collection;
use OriginEngine\Pipeline\Task;
use OriginEngine\Helpers\Terminal\Executor;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;
use OriginEngine\Pipeline\TaskResponse;

class WaitForDocker extends Task
{

    protected function execute(WorkingDirectory $workingDirectory, Collection $config): TaskResponse
    {
        $output = '';
        while(!$output) {
            $output = Executor::cd($workingDirectory)
                ->execute('docker ps -q --filter health=starting');
            if(!$output) {
                sleep(2);
            }
        }
    }

    protected function undo(WorkingDirectory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        // No down method
    }

    protected function upName(Collection $config): string
    {
        return 'Waiting for Docker';
    }

    protected function downName(Collection $config): string
    {
        return 'Waiting for Docker';
    }
}
