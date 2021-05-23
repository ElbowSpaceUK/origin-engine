<?php

namespace OriginEngine\Pipeline\Tasks;

use OriginEngine\Contracts\Pipeline\Task;
use OriginEngine\Helpers\Terminal\Executor;
use OriginEngine\Pipeline\ProvisionedTask;

class BringEnvironmentUp extends Task
{

    public static function provision(bool $removeContainersOnDown): ProvisionedTask
    {
        return ProvisionedTask::provision(static::class)
            ->dependencies([
                'removeContainersOnDown' => $removeContainersOnDown
            ]);
    }

    public function up(\OriginEngine\Helpers\WorkingDirectory\WorkingDirectory $workingDirectory): void
    {
        Executor::cd($workingDirectory)
            ->execute('./vendor/bin/sail up -d');
    }

    public function down(\OriginEngine\Helpers\WorkingDirectory\WorkingDirectory $workingDirectory): void
    {
        $command = './vendor/bin/sail down';
        if ($this->config->get('removeContainersOnDown', true)) {
            $command .= ' -v';
        }
        Executor::cd($workingDirectory)
            ->execute($command);
    }

}
