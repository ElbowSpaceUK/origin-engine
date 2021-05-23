<?php

namespace OriginEngine\Pipeline;

trait HasEvents
{
    private array $events = [];

    private array $globalEvents = [];

    public function addEvent(string $event, string $task, \Closure $closure)
    {
        if(!array_key_exists($event, $this->events)) {
            $this->events[$event] = [];
        }
        if(!array_key_exists($task, $this->events[$event])) {
            $this->events[$event][$task] = [];
        }
        $this->events[$event][$task][] = $closure;
    }

    public function addGlobalEvent(string $event, \Closure $closure)
    {
        if(!array_key_exists($event, $this->globalEvents)) {
            $this->globalEvents[$event] = [];
        }
        $this->globalEvents[$event] = $closure;
    }

    public function getEvents(string $event, string $task)
    {
        return array_merge($this->getGlobalEvents($event),
            (array_key_exists($event, $this->events) && array_key_exists($task, $this->events[$event]) ? $this->events[$event][$task] : [])
        );
    }

    protected function getGlobalEvents(string $event): array
    {
        return array_key_exists($event, $this->globalEvents) ? $this->globalEvents[$event] : [];
    }

}
