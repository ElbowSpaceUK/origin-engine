<?php

namespace OriginEngine\Pipeline\Tasks\LaravelSail;

use Illuminate\Support\Collection;
use OriginEngine\Pipeline\Task;
use OriginEngine\Helpers\Terminal\Executor;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;
use OriginEngine\Pipeline\Old\ProvisionedTask;
use OriginEngine\Pipeline\TaskResponse;

class MigrateDatabase extends Task
{

    /**
     * @param string $environment The environment to migrate
     */
    public function __construct(string $environment)
    {
        parent::__construct([
            'environment' => $environment
        ]);
    }

    protected function execute(WorkingDirectory $workingDirectory, Collection $config): TaskResponse
    {
        $output = Executor::cd($workingDirectory)->execute(
            sprintf('./vendor/bin/sail artisan migrate --env=%s', $config->get('environment'))
        );

        if($output) {
            $this->writeDebug($output);
        }

        return $this->succeeded([
            'output' => $output
        ]);
    }

    protected function undo(WorkingDirectory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        $output = Executor::cd($workingDirectory)->execute(
            sprintf('./vendor/bin/sail artisan migrate:rollback --env=%s', $config->get('environment'))
        );

    }

    protected function upName(Collection $config): string
    {
        return sprintf('Migrating the %s database', $config->get('environment'));
    }

    protected function downName(Collection $config): string
    {
        return sprintf('Rolling back the %s database', $config->get('environment'));
    }

}
