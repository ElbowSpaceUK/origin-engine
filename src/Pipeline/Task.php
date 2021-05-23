<?php

namespace OriginEngine\Pipeline;

use Illuminate\Support\Collection;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;

abstract class Task
{
    use CreatesTaskResponse;

    private array $defaultConfiguration;

    private string $upName;

    private string $downName;

    public function __construct(array $defaultConfiguration = [])
    {
        $this->defaultConfiguration = $defaultConfiguration;
    }

    public function run(WorkingDirectory $workingDirectory, Collection $config): TaskResponse
    {
        try {
            return $this->execute($workingDirectory, $config);
        } catch (\Exception $e) {
            $this->writeError(sprintf('[%s] at %s, line %u', $e->getMessage(), $e->getFile(), $e->getCode()));
            $this->writeDebug($e->getTraceAsString());
            return $this->failed();
        }
    }

    public function reverse(WorkingDirectory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        $this->undo($workingDirectory, $status, $config, $output);
    }

    public function getDefaultConfiguration(): array
    {
        return $this->defaultConfiguration;
    }

    abstract protected function execute(WorkingDirectory $workingDirectory, Collection $config): TaskResponse;

    abstract protected function undo(WorkingDirectory $workingDirectory, bool $status, Collection $config, Collection $output): void;

    abstract protected function upName(Collection $config): string;

    abstract protected function downName(Collection $config): string;

    public function setUpName(string $name)
    {
        $this->upName = $name;

        return $this;
    }

    public function setDownName(string $name)
    {
        $this->downName = $name;

        return $this;
    }

    public function getUpName(Collection $config): string
    {
        if(isset($this->upName)) {
            return $this->upName;
        }
        return $this->upName($config);
    }

    public function getDownName(Collection $config): string
    {
        if(isset($this->downName)) {
            return $this->downName;
        }
        return $this->downName($config);
    }

}
