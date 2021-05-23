<?php

namespace OriginEngine\Pipeline\Old\Tasks;

use OriginEngine\Contracts\Pipeline\Task;
use OriginEngine\Helpers\Terminal\Executor;
use OriginEngine\Pipeline\Old\ProvisionedTask;

class GenerateApplicationKey extends Task
{

    public static function provision(string $environment = 'local')
    {
        return ProvisionedTask::provision(static::class)
            ->dependencies([
                'environment' => $environment
            ]);
    }

    public function up(\OriginEngine\Helpers\WorkingDirectory\WorkingDirectory $workingDirectory): void
    {
        Executor::cd($workingDirectory)->execute(
            sprintf('./vendor/bin/sail artisan key:generate --env=%s', $this->config->get('environment'))
        );
    }

    public function down(\OriginEngine\Helpers\WorkingDirectory\WorkingDirectory $workingDirectory): void
    {
        // No down tasks
    }

}
