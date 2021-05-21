<?php

namespace App\Core\Pipeline\Tasks;

use App\Core\Contracts\Pipeline\Task;
use App\Core\Helpers\Terminal\Executor;
use App\Core\Pipeline\ProvisionedTask;

class MigrateDatabase extends Task
{

    public static function provision(string $environment = 'local')
    {
        return ProvisionedTask::provision(static::class)
            ->dependencies([
                'environment' => $environment
            ]);
    }

    public function up(\App\Core\Helpers\WorkingDirectory\WorkingDirectory $workingDirectory): void
    {
        Executor::cd($workingDirectory)->execute(
            sprintf('./vendor/bin/sail artisan migrate --env=%s', $this->config->get('environment'))
        );
    }

    public function down(\App\Core\Helpers\WorkingDirectory\WorkingDirectory $workingDirectory): void
    {
        // TODO Enable once migrations fixed

        //    Executor::cd($workingDirectory)->execute(
        //        sprintf('./vendor/bin/sail artisan migrate:rollback --env=%s', $this->config->get('environment'))
        //    );
    }

}
