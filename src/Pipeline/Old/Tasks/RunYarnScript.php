<?php

namespace OriginEngine\Pipeline\Old\Tasks;

use OriginEngine\Contracts\Pipeline\Task;
use OriginEngine\Helpers\Terminal\Executor;
use OriginEngine\Pipeline\Old\ProvisionedTask;

class RunYarnScript extends Task
{

    public static function provision(string $script, string $cwd = ''): \OriginEngine\Pipeline\Old\ProvisionedTask
    {
        return ProvisionedTask::provision(static::class)
            ->dependencies([
                'script' => $script,
                'cwd' => $cwd
            ]);
    }

    public function up(\OriginEngine\Helpers\WorkingDirectory\WorkingDirectory $workingDirectory): void
    {
        $command = './vendor/bin/sail yarn';

        if($this->config->get('cwd')) {
            $command .= sprintf(' --cwd %s', $this->config->get('cwd'));
        }

        $command .= sprintf(' run %s --non-interactive --no-progress', $this->config->get('script'));

        Executor::cd($workingDirectory)->execute($command);
    }

    public function down(\OriginEngine\Helpers\WorkingDirectory\WorkingDirectory $workingDirectory): void
    {
        // No down tasks
    }

}
