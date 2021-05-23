<?php

namespace OriginEngine\Pipeline\Old\Tasks;

use OriginEngine\Contracts\Pipeline\Task;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Terminal\Executor;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;

class WaitForDocker extends Task
{

    public function up(WorkingDirectory $workingDirectory): void
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

    public function down(WorkingDirectory $workingDirectory): void
    {
        // No down tasks
    }
}
