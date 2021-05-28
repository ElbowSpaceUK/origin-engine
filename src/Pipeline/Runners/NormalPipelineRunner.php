<?php

namespace OriginEngine\Pipeline\Runners;

use OriginEngine\Contracts\Pipeline\PipelineRunner as PipelineRunnerContract;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Pipeline;
use OriginEngine\Pipeline\PipelineConfig;
use OriginEngine\Pipeline\PipelineHistory;
use function collect;

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
            IO::task('Running task ' . $task->getUpName(collect($config->getAll($taskKey))), fn() => '');
        });

        $pipeline->addGlobalEvent(Pipeline::BEFORE_DOWN_EVENT, function(PipelineHistory $history, string $taskKey) use ($pipeline) {
            $task = $pipeline->getTask($taskKey);
            IO::task(sprintf('Undoing Task: %s' , $task->getDownName($history->getConfig($taskKey))), fn() => '');
        });

        return $this->baseRunner->run($pipeline, $config, $workingDirectory);
    }
}
