<?php

namespace App\Core\Pipeline;

use App\Core\Contracts\Pipeline\Task;
use App\Core\Helpers\WorkingDirectory\WorkingDirectory;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;

class ProvisionedTask
{
    // TODO Make it possible to 'warn' from a task, maybe by throwing a specific exception. Could then catch and show message on CLI.

    private TaskConfig $config;

    private string $taskClass;

    private Task $task;

    private string $name;

    public function __construct(string $taskClass)
    {
        $this->taskClass = $taskClass;
        $this->config = new TaskConfig();
    }

    public static function provision(string $taskClass): ProvisionedTask
    {
        return new static($taskClass);
    }

    public function dependencies($config)
    {
        $this->config = TaskConfig::parse($config);

        return $this;
    }

    public function getTask(): Task
    {
        if(!isset($this->task)) {
            $this->task = app($this->taskClass, ['config' => $this->config]);
        }
        return $this->task;
    }

    public function name()
    {
        if(!isset($this->name)) {
            $reflectionClass = new \ReflectionClass($this->taskClass);
            $this->name =
                str_replace(
                    '-', ' ',
                    Str::title(
                        Str::kebab(
                            $reflectionClass->getShortName()
                        )
                    )
                );
        }
        return $this->name;
    }

    public function withName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function up(WorkingDirectory $workingDirectory): void
    {
        $this->getTask()->up($workingDirectory);
    }

    public function down(WorkingDirectory $workingDirectory): void
    {
        $this->getTask()->down($workingDirectory);
    }

}
