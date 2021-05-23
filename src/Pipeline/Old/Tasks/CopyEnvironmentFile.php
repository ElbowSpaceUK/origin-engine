<?php

namespace OriginEngine\Pipeline\Tasks;

use OriginEngine\Contracts\Pipeline\Task;
use OriginEngine\Helpers\Env\EnvRepository;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;
use OriginEngine\Pipeline\ProvisionedTask;

class CopyEnvironmentFile extends Task
{

    public static function provision(string $template, string $destination, array $overrides = [])
    {
        return ProvisionedTask::provision(static::class)
            ->dependencies([
                'template' => $template,
                'destination' => $destination,
                'overrides' => $overrides
            ]);
    }

    public function up(\OriginEngine\Helpers\WorkingDirectory\WorkingDirectory $workingDirectory): void
    {

        Filesystem::create()->copy(
            $this->getEnvFilePath($workingDirectory, $this->config->get('template')),
            $this->getEnvFilePath($workingDirectory, $this->config->get('destination'))
        );

        $overrides = $this->config->get('overrides', []);
        if (count($overrides) > 0) {
            $envRepository = new EnvRepository($workingDirectory);
            $env = $envRepository->get($this->config->get('destination'));

            foreach ($overrides as $name => $value) {
                $env->setVariable($name, $value);
            }

            $envRepository->update($env);
        }
    }

    private function getEnvFilePath(WorkingDirectory $workingDirectory, string $envFileName)
    {
        return Filesystem::append($workingDirectory->path(), $envFileName);
    }

    public function down(\OriginEngine\Helpers\WorkingDirectory\WorkingDirectory $workingDirectory): void
    {
        Filesystem::create()->remove(
            $this->getEnvFilePath($workingDirectory, $this->config->get('destination'))
        );
    }
}
