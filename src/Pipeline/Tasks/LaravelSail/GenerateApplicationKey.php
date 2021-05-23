<?php

namespace OriginEngine\Pipeline\Tasks\LaravelSail;

use Illuminate\Support\Collection;
use OriginEngine\Helpers\Env\EnvRepository;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;
use OriginEngine\Pipeline\Task;
use OriginEngine\Pipeline\TaskResponse;

class GenerateApplicationKey extends Task
{

    public function __construct(string $environment)
    {
        parent::__construct([
            'environment' => $environment
        ]);
    }

    protected function execute(WorkingDirectory $workingDirectory, Collection $config): TaskResponse
    {
        $filename = '.'  .$config->get('environment') . '.env';
        $envRepository = new EnvRepository($workingDirectory);
        $env = $envRepository->get($filename);
        $oldAppKey = $env->getVariable('APP_KEY');

        Executor::cd($workingDirectory)->execute(
            sprintf('./vendor/bin/sail artisan key:generate --env=%s', $config->get('environment'))
        );

        return $this->succeeded([
            'old_app_key' => $oldAppKey
        ]);
    }

    protected function undo(WorkingDirectory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        $filename = '.'  .$config->get('environment') . '.env';

        $envRepository = new EnvRepository($workingDirectory);
        $env = $envRepository->get($filename);

        $env->setVariable('APP_KEY', $output->get('old_app_key'));

        $envRepository->update($env, $config->get('fileName'));
    }

    protected function upName(Collection $config): string
    {
        return sprintf('Generate %s application key', $config->get('environment'));
    }

    protected function downName(Collection $config): string
    {
        return sprintf('Rollback %s application key', $config->get('environment'));
    }

}
