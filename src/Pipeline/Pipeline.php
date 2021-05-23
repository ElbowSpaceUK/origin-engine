<?php

namespace OriginEngine\Pipeline;

abstract class Pipeline
{

    private array $beforeEvents = [];

    private array $afterEvents = [];

    /**
     * @return array|Task[]
     */
    abstract public function getTasks(): array;

    public function boot() {
        // Override this method to set up custom events on pipelines.
    }

    public function before(string $task, \Closure $event)
    {
        if(!array_key_exists($task, $this->beforeEvents)) {
            $this->beforeEvents[$task] = [];
        }
        $this->beforeEvents[$task][] = $event;
    }

    public function after(string $task, \Closure $event)
    {
        if(!array_key_exists($task, $this->afterEvents)) {
            $this->afterEvents[$task] = [];
        }
        $this->afterEvents[$task][] = $event;
    }

    public function getBeforeEventsForTask(string $task): array
    {
        return array_key_exists($task, $this->beforeEvents) ? $this->beforeEvents[$task] : [];
    }

    public function getAfterEventsForTask(string $task): array
    {
        return array_key_exists($task, $this->afterEvents) ? $this->afterEvents[$task] : [];
    }
}
