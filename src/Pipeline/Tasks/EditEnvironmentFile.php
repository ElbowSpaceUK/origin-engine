<?php

namespace OriginEngine\Pipeline\Tasks;

use Illuminate\Support\Collection;
use OriginEngine\Pipeline\Task;
use OriginEngine\Helpers\Env\EnvRepository;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Old\ProvisionedTask;
use OriginEngine\Pipeline\TaskResponse;

class EditEnvironmentFile extends Task
{

    /**
     * CopyEnvironmentFile constructor.
     * @param array $replace A key-pair array of environment variables to update or add, and the new values.
     * @param string $fileName The name of the environment file within the working directory
     */
    public function __construct(array $replace, string $fileName = '.env')
    {
        parent::__construct([
            'replace' => $replace,
            'fileName' => $fileName
        ]);
    }

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        $envRepository = new EnvRepository($workingDirectory);
        $env = $envRepository->get($config->get('fileName'));

        $currentData = [];
        foreach ($config->get('replace') as $name => $value) {
            $currentData[$name] = $env->getVariable($name, null);
            $this->writeInfo(
                sprintf('Replacing %s with %s. ', $name, $value) .
                ($currentData[$name] === null ?: sprintf('Old value was %s. ', $currentData[$name]))
            );
            $env->setVariable($name, $value);
        }
        $this->export('replaced', $currentData);

        $envRepository->update($env, $config->get('fileName'));

        return $this->succeeded();
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        $envRepository = new EnvRepository($workingDirectory);
        $env = $envRepository->get($config->get('fileName'));

        foreach ($output->get('replaced') as $name => $value) {
            if($value === null) {
                $env->removeVariable($name);
            } else {
                $env->setVariable($name, $value);
            }
        }

        $envRepository->update($env, $config->get('fileName'));
    }

    protected function upName(Collection $config): string
    {
        return 'Editing environment file';
    }

    protected function downName(Collection $config): string
    {
        return 'Reverting environment file';
    }
}
