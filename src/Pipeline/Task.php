<?php

namespace OriginEngine\Pipeline;

use Illuminate\Support\Collection;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;

abstract class Task
{
    use CreatesTaskResponse;

    private array $defaultConfiguration;

    public function __construct(array $defaultConfiguration = [])
    {
        $this->defaultConfiguration = $defaultConfiguration;
    }

    public function run(WorkingDirectory $workingDirectory, Collection $config)
    {
        return $this->execute($workingDirectory, $config);
    }

    public function getDefaultConfiguration(): array
    {
        return $this->defaultConfiguration;
    }

    abstract public function execute(WorkingDirectory $workingDirectory, Collection $config);

}
