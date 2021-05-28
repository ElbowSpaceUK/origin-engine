<?php

namespace OriginEngine\Pipeline;

abstract class Pipeline
{
    use HasEvents;

    public const BEFORE_EVENT = 'before';
    public const AFTER_EVENT = 'after';
    public const BEFORE_DOWN_EVENT = 'beforeDown';
    public const AFTER_DOWN_EVENT = 'afterDown';

    private string $alias = self::class;

    private array $afterTasks = [];

    private array $beforeTasks = [];


    /**
     * @return array|Task[]
     */
    abstract protected function tasks(): array;

    public function getTasks(): array
    {
        $tasks = [];
        $defaultTasks = $this->tasks();
        foreach($defaultTasks as $defaultTaskKeyLoop => $taskLoop) {
            if(array_key_exists($defaultTaskKeyLoop, $this->beforeTasks)) {
                foreach($this->beforeTasks[$defaultTaskKeyLoop] as $newTaskKey => $newTask) {
                    $tasks[$newTaskKey] = $newTask;
                }
            }
            $tasks[$defaultTaskKeyLoop] = $taskLoop;
            if(array_key_exists($defaultTaskKeyLoop, $this->afterTasks)) {
                foreach($this->afterTasks[$defaultTaskKeyLoop] as $newTaskKey => $newTask) {
                    $tasks[$newTaskKey] = $newTask;
                }
            }
        }
        return $tasks;
    }

    public function runTaskAfter(string $after, string $taskName, Task $task)
    {
        if(!array_key_exists($after, $this->afterTasks)) {
            $this->afterTasks[$after] = [];
        }
        $this->afterTasks[$after][$taskName] = $task;
    }

    public function runTaskBefore(string $before, string $taskName, Task $task)
    {
        if(!array_key_exists($before, $this->beforeTasks)) {
            $this->beforeTasks[$before] = [];
        }
        $this->beforeTasks[$before][$taskName] = $task;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function setAlias(string $alias)
    {
        $this->alias = $alias;
    }

    public function getTask(string $taskAlias): Task
    {
        $tasks = $this->getTasks();
        if(array_key_exists($taskAlias, $tasks)) {
            return $tasks[$taskAlias];
        }
        throw new \Exception(
            sprintf('Could not find task with alias [%s]', $taskAlias)
        );
    }

    public function before(string $task, \Closure $event)
    {
        $this->addEvent(static::BEFORE_EVENT, $task, $event);
    }

    public function after(string $task, \Closure $event)
    {
        $this->addEvent(static::AFTER_EVENT, $task, $event);
    }

    public function beforeDown(string $task, \Closure $event)
    {
        $this->addEvent(STATIC::BEFORE_DOWN_EVENT, $task, $event);
    }

    public function afterDown(string $task, \Closure $event)
    {
        $this->addEvent(static::AFTER_DOWN_EVENT, $task, $event);
    }

    public function getBeforeEvents(string $task): array
    {
        return $this->getEvents(static::BEFORE_EVENT, $task);
    }

    public function getAfterEvents(string $task): array
    {
        return $this->getEvents(static::AFTER_EVENT, $task);
    }

    public function getBeforeDownEvents(string $task): array
    {
        return $this->getEvents(static::BEFORE_DOWN_EVENT, $task);
    }

    public function getAfterDownEvents(string $task): array
    {
        return $this->getEvents(static::AFTER_DOWN_EVENT, $task);
    }

    public function aliasedConfig(): array
    {
        return [];
    }

}
