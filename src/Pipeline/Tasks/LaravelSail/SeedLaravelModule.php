<?php

namespace OriginEngine\Pipeline\Tasks\LaravelSail;

use Illuminate\Support\Collection;
use OriginEngine\Pipeline\Task;
use OriginEngine\Helpers\Terminal\Executor;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;
use OriginEngine\Pipeline\Old\ProvisionedTask;
use OriginEngine\Pipeline\TaskResponse;

class SeedLaravelModule extends Task
{

    /**
     * @param string $environment The environment to migrate
     */
    public function __construct(string $module, string $class, string $environment)
    {
        parent::__construct([
            'module' => $module,
            'class' => $class,
            'environment' => $environment
        ]);
    }

    protected function execute(WorkingDirectory $workingDirectory, Collection $config): TaskResponse
    {
        $output = Executor::cd($workingDirectory)->execute(sprintf(
            './vendor/bin/sail artisan module:seed %s --class=%s --env=%s',
            $config->get('module'),
            $config->get('class'),
            $config->get('environment'),
        ));
        $this->writeDebug(sprintf('module:seed output: %s', $output));

        return $this->succeeded();
    }

    protected function undo(WorkingDirectory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        // Unable to undo a seed action
    }

    protected function upName(Collection $config): string
    {
        return sprintf('Seeding the %s module', $config->get('module'));
    }

    protected function downName(Collection $config): string
    {
        return sprintf('Seeding cannot be rolled back');
    }

}
