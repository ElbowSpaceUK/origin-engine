<?php

namespace OriginEngine\Pipeline;

use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Directory\Directory;

class PipelineRunner implements \OriginEngine\Contracts\Pipeline\PipelineRunner
{

    public function run(Pipeline $pipeline, PipelineConfig $config, Directory $workingDirectory): PipelineHistory
    {
        $tasks = $pipeline->getTasks();
        $history = new PipelineHistory();
        $config = $this->gatherConfiguration($pipeline, $config);

        foreach($tasks as $key => $task) {
            foreach($pipeline->getBeforeEvents($key) as $event) {
                $result = $event($config, $history, $key);
                if($result === false) {
                    continue 2;
                }
            }

            $taskConfig = collect($config->getAll($key));
            $response = $task->run($workingDirectory, $taskConfig);
            $history->add($key, $response->isSuccess(), $response->getMessages(), $response->getData(), $taskConfig->toArray());
            if($response->isSuccess() === false) {
                $this->undo($pipeline, $workingDirectory, $history, $key);
                return $history;
            }

            foreach($pipeline->getAfterEvents($key) as $event) {
                $event($config, $history, $key);
            }
        }
        return $history;
    }

    protected function undo(Pipeline $pipeline, Directory $workingDirectory, PipelineHistory $history, string $startFrom = null)
    {
        $downRunner = app(\OriginEngine\Contracts\Pipeline\PipelineDownRunner::class);
        $downRunner->run($pipeline, $workingDirectory, $history, $startFrom);
    }

    public function gatherConfiguration(Pipeline $pipeline, PipelineConfig $config): PipelineConfig
    {
        foreach($pipeline->getTasks() as $key => $task) {
            $defaultTaskConfig = $task->getDefaultConfiguration();
            foreach($defaultTaskConfig as $defaultKey => $defaultValue) {
                if(!$config->has($key, $defaultKey)) {
                    $config->add($key, $defaultKey, $defaultValue);
                }
            }
        }
        foreach($pipeline->aliasedConfig() as $alias => $realKey) {
            foreach($config->getAliasedConfiguration() as $givenAlias => $givenValue) {
                if($givenAlias === $alias) {
                    $config->addWithKeyInConfigName($realKey, $givenValue);
                }
            }
        }
        return $config;
    }

}
