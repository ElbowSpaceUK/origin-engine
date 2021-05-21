<?php

namespace App\Core\Pipeline\Tasks;

use App\Core\Contracts\Pipeline\Task;
use App\Core\Helpers\IO\IO;
use App\Core\Helpers\Terminal\Executor;
use App\Core\Helpers\WorkingDirectory\WorkingDirectory;

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
