<?php

namespace OriginEngine\Pipeline;

use OriginEngine\Contracts\Pipeline\PipelineRunner as PipelineRunnerContract;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Directory\Directory;

class NormalPipelineRunner implements PipelineRunnerContract
{

    private PipelineRunnerContract $baseRunner;

    public function __construct(PipelineRunnerContract $baseRunner)
    {
        $this->baseRunner = $baseRunner;
    }

    public function run(Pipeline $pipeline, PipelineConfig $config, Directory $workingDirectory): PipelineHistory
    {
        $pipeline->addGlobalEvent(Pipeline::BEFORE_EVENT, function(PipelineConfig $config, PipelineHistory $history, string $taskKey) use ($pipeline) {
            $task = $pipeline->getTask($taskKey);
            IO::info('Running task ' . $task->getUpName(collect($config->getAll($taskKey))));
        });

        $pipeline->addGlobalEvent(Pipeline::BEFORE_DOWN_EVENT, function(PipelineConfig $config, PipelineHistory $history, string $taskKey) use ($pipeline) {
            $task = $pipeline->getTask($taskKey);
            IO::info('Undoing task ' . $task->getDownName(collect($config->getAll($taskKey))));
        });

        return $this->baseRunner->run($pipeline, $config, $workingDirectory);
    }
}
