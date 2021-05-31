<?php

namespace OriginEngine\Pipeline\Tasks\Utils;

use Illuminate\Support\Collection;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Task;
use OriginEngine\Pipeline\TaskResponse;

class CreateAndRunTask extends Task
{

    public function __construct(\Closure $taskCreator, array $configuration, string $upName, string $downName)
    {
        parent::__construct([
            'task-creator' => $taskCreator,
            'configuration' => $configuration,
            'up-name' => $upName,
            'down-name' => $downName
        ]);
    }

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        $task = $config->get('task-creator')();

        $taskConfig = $task->getDefaultConfiguration();
        foreach($config->get('configuration') as $configKey => $configValue) {
            $taskConfig[$configKey] = $configValue;
        }

        return $task->run($workingDirectory, collect($taskConfig));
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        $task = $config->get('task-creator')();

        $taskConfig = $task->getDefaultConfiguration();
        foreach($config as $configKey => $configValue) {
            $taskConfig[$configKey] = $configValue;
        }

        $task->reverse($workingDirectory, $status, collect($taskConfig), $output);
    }

    protected function upName(Collection $config): string
    {
        return $config->get('up-name');
    }

    protected function downName(Collection $config): string
    {
        return $config->get('down-name');
    }
}
