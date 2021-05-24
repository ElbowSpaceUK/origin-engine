<?php

namespace OriginEngine\Pipeline;

use OriginEngine\Contracts\Pipeline\PipelineRunner as PipelineRunnerContract;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;

class VerbosePipelineRunner extends NormalPipelineRunner implements PipelineRunnerContract
{

    public function run(Pipeline $pipeline, PipelineConfig $config, WorkingDirectory $workingDirectory): PipelineHistory
    {
        $pipeline->addGlobalEvent(Pipeline::AFTER_EVENT, function(PipelineConfig $config, PipelineHistory $history, string $taskKey) use ($pipeline) {
            $task = $pipeline->getTask($taskKey);
            IO::info('Ran task ' . $task->getUpName(collect($config->getAll($taskKey))));
        });

        $pipeline->addGlobalEvent(Pipeline::AFTER_DOWN_EVENT, function(PipelineConfig $config, PipelineHistory $history, string $taskKey) use ($pipeline) {
            $task = $pipeline->getTask($taskKey);
            IO::info('Undone task ' . $task->getDownName(collect($config->getAll($taskKey))));
        });

        return parent::run($pipeline, $config, $workingDirectory);
    }
}
