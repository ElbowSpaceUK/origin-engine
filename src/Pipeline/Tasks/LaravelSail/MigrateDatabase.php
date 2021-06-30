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
    public function __construct(string $environment, ?bool $valet = null)
    {
        parent::__construct([
            'environment' => $environment,
            'valet' => $valet
        ]);
    }

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        $command = './vendor/bin/sail artisan migrate';

        if($config->get('valet')) {
            $command = 'php artisan migrate:fresh';
        }

        $output = Executor::cd($workingDirectory)->execute(
            sprintf('%s --env=%s',$command, $config->get('environment'))
        );

        $this->writeDebug('artisan migrate output: ' . $output);

        return $this->succeeded([
            'output' => $output
        ]);
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        $command = './vendor/bin/sail artisan migrate:rollback';
        if($config->get('valet')) {
            $command = 'php artisan migrate:rollback';
        }

        Executor::cd($workingDirectory)->execute(
            sprintf('%s --env=%s',$command, $config->get('environment'))
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
