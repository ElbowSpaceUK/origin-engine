<?php

namespace OriginEngine\Pipeline\Tasks;

use OriginEngine\Contracts\Pipeline\Task;
use OriginEngine\Helpers\Terminal\Executor;
use OriginEngine\Pipeline\Old\ProvisionedTask;

class SeedLaravelModule extends Task
{

    public static function provision(string $module, string $class, string $environment = 'local'): ProvisionedTask
    {
        return ProvisionedTask::provision(static::class)
            ->dependencies([
                'module' => $module, 'class' => $class, 'environment' => 'local'
            ]);
    }

    public function up(\OriginEngine\Helpers\WorkingDirectory\WorkingDirectory $workingDirectory): void
    {
        Executor::cd($workingDirectory)->execute(sprintf(
            './vendor/bin/sail artisan module:seed %s --class=%s --env=%s',
            $this->config->get('module'),
            $this->config->get('class'),
            $this->config->get('environment'),
        ));
    }

    public function down(\OriginEngine\Helpers\WorkingDirectory\WorkingDirectory $workingDirectory): void
    {
        // No down tasks
    }

}
