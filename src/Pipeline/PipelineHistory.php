<?php

namespace OriginEngine\Pipeline;

use Illuminate\Support\Collection;

class PipelineHistory
{

    private array $history = [];

    public function add(string $taskKey, bool $isSuccess, array $messages, array $output, array $config)
    {
        $this->history[$taskKey] = [
            'status' => $isSuccess,
            'messages' => $messages,
            'output' => collect($output),
            'config' => collect($config)
        ];
    }

    public function succeeded(string $taskKey): bool
    {
        return $this->getTaskHistory($taskKey)['status'] === true;
    }

    public function failed(string $taskKey): bool
    {
        return !$this->succeeded($taskKey);
    }

    public function hasRun(string $taskKey)
    {
        return array_key_exists($taskKey, $this->history);
    }

    public function getTaskHistory(string $taskKey): array
    {
        if($this->hasRun($taskKey)) {
            return $this->history[$taskKey];
        }
        throw new \Exception(sprintf('Task [%s] has not run', $taskKey));
    }

    public function getOutput(string $taskKey): Collection
    {
        return collect($this->getTaskHistory($taskKey)['output']);
    }

    public function getConfig(string $taskKey): Collection
    {
        return collect($this->getTaskHistory($taskKey)['config']);
    }

    public function getMessages(string $taskKey): Collection
    {
        return collect($this->getTaskHistory($taskKey)['messages']);
    }

    public function getRunTasks(): array
    {
        return array_keys($this->history);
    }

    public function allSuccessful(): bool
    {
        foreach($this->getRunTasks() as $task) {
            if(!$this->succeeded($task)) {
                return false;
            }
        }
        return true;
    }

}
