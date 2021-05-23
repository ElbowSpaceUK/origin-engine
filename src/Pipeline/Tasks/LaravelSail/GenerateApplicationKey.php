<?php

namespace OriginEngine\Pipeline\Tasks\LaravelSail;

use Illuminate\Support\Collection;
use OriginEngine\Helpers\Env\EnvRepository;
use OriginEngine\Helpers\Terminal\Executor;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;
use OriginEngine\Pipeline\Task;
use OriginEngine\Pipeline\TaskResponse;

class GenerateApplicationKey extends Task
{

    public function __construct(string $environment, string $environmentFile = '.env')
    {
        parent::__construct([
            'environment' => $environment,
            'environmentFile' => $environmentFile
        ]);
    }

    protected function execute(WorkingDirectory $workingDirectory, Collection $config): TaskResponse
    {
        $filename = $config->get('environmentFile') ?? '.env';

        $this->writeInfo('Editing ' . $filename);

        $envRepository = new EnvRepository($workingDirectory);
        $env = $envRepository->get($filename);
        $this->export('old_app_key', $env->getVariable('APP_KEY'));
        $this->writeInfo('Old app key backed up');

        $output = Executor::cd($workingDirectory)->execute(
            sprintf('./vendor/bin/sail artisan key:generate --env=%s', $config->get('environment'))
        );

        $this->writeSuccess('Generated a new key');
        $this->writeDebug('key:generate output: ' . $output);

        return $this->succeeded();
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
