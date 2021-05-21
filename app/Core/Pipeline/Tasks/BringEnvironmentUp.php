<?php

namespace App\Core\Pipeline\Tasks;

use App\Core\Contracts\Pipeline\Task;
use App\Core\Helpers\Terminal\Executor;
use App\Core\Pipeline\ProvisionedTask;

class BringEnvironmentUp extends Task
{

    public static function provision(bool $removeContainersOnDown): ProvisionedTask
    {
        return ProvisionedTask::provision(static::class)
            ->dependencies([
                'removeContainersOnDown' => $removeContainersOnDown
            ]);
    }

    public function up(\App\Core\Helpers\WorkingDirectory\WorkingDirectory $workingDirectory): void
    {
        Executor::cd($workingDirectory)
            ->execute('./vendor/bin/sail up -d');
    }

    public function down(\App\Core\Helpers\WorkingDirectory\WorkingDirectory $workingDirectory): void
    {
        $command = './vendor/bin/sail down';
        if ($this->config->get('removeContainersOnDown', true)) {
            $command .= ' -v';
        }
        Executor::cd($workingDirectory)
            ->execute($command);
    }

}
