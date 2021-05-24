<?php

namespace OriginEngine\Pipeline\Tasks\LaravelSail;

use Illuminate\Support\Collection;
use OriginEngine\Pipeline\Task;
use OriginEngine\Helpers\Terminal\Executor;
use OriginEngine\Helpers\Directory\Directory;
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

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        $output = Executor::cd($workingDirectory)->execute(
            sprintf('./vendor/bin/sail artisan migrate --env=%s', $config->get('environment'))
        );

        $this->writeDebug('artisan migrate output: ' . $output);

        return $this->succeeded([
            'output' => $output
        ]);
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        Executor::cd($workingDirectory)->execute(
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
