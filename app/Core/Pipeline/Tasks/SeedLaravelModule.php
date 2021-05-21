<?php

namespace App\Core\Pipeline\Tasks;

use App\Core\Contracts\Pipeline\Task;
use App\Core\Helpers\Terminal\Executor;
use App\Core\Pipeline\ProvisionedTask;

class SeedLaravelModule extends Task
{

    public static function provision(string $module, string $class, string $environment = 'local'): ProvisionedTask
    {
        return ProvisionedTask::provision(static::class)
            ->dependencies([
                'module' => $module, 'class' => $class, 'environment' => 'local'
            ]);
    }

    public function up(\App\Core\Helpers\WorkingDirectory\WorkingDirectory $workingDirectory): void
    {
        Executor::cd($workingDirectory)->execute(sprintf(
            './vendor/bin/sail artisan module:seed %s --class=%s --env=%s',
            $this->config->get('module'),
            $this->config->get('class'),
            $this->config->get('environment'),
        ));
    }

    public function down(\App\Core\Helpers\WorkingDirectory\WorkingDirectory $workingDirectory): void
    {
        // No down tasks
    }

}
