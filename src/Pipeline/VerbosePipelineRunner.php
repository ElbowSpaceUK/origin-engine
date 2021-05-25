<?php

namespace OriginEngine\Pipeline;

use OriginEngine\Contracts\Pipeline\PipelineRunner as PipelineRunnerContract;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Directory\Directory;

class VerbosePipelineRunner extends NormalPipelineRunner implements PipelineRunnerContract
{

    public function run(Pipeline $pipeline, PipelineConfig $config, Directory $workingDirectory): PipelineHistory
    {
        $pipeline->addGlobalEvent(Pipeline::AFTER_EVENT, function(PipelineConfig $config, PipelineHistory $history, string $taskKey) use ($pipeline) {
            $task = $pipeline->getTask($taskKey);
            IO::info('Ran task ' . $task->getUpName(collect($config->getAll($taskKey))));
        });

        $pipeline->addGlobalEvent(Pipeline::AFTER_DOWN_EVENT, function(PipelineHistory $history, string $taskKey) use ($pipeline) {
            $task = $pipeline->getTask($taskKey);
            IO::info('Undone task ' . $task->getDownName($history->getConfig($taskKey)));
        });

        return parent::run($pipeline, $config, $workingDirectory);
    }
}
