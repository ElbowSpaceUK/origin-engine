<?php

namespace OriginEngine\Pipeline\Tasks;

use Illuminate\Support\Collection;
use OriginEngine\Pipeline\Task;
use OriginEngine\Helpers\Terminal\Executor;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\TaskResponse;

class WaitForDocker extends Task
{

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        $startTime = microtime(true);

        $output = 'not-empty';
        while($output !== '') {
            $output = Executor::cd($workingDirectory)
                ->execute('docker ps -q --filter health=starting');
            if($output !== '') {
                sleep(2);
            }
        }
        return $this->succeeded([
            'waiting_time' => microtime(true) - $startTime,
            'output' => $output
        ]);
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
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
