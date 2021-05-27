<?php

namespace OriginEngine\Pipeline\Runners;

use OriginEngine\Contracts\Pipeline\PipelineRunner as PipelineRunnerContract;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Pipeline;
use OriginEngine\Pipeline\PipelineConfig;
use OriginEngine\Pipeline\PipelineHistory;
use OriginEngine\Pipeline\Runners\NormalPipelineRunner;
use function collect;

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
