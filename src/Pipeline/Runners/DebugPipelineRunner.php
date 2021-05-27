<?php

namespace OriginEngine\Pipeline\Runners;

use OriginEngine\Contracts\Pipeline\PipelineRunner as PipelineRunnerContract;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Pipeline;
use OriginEngine\Pipeline\PipelineConfig;
use OriginEngine\Pipeline\PipelineHistory;
use OriginEngine\Pipeline\Runners\VeryVerbosePipelineRunner;

class DebugPipelineRunner extends VeryVerbosePipelineRunner implements PipelineRunnerContract
{

    public function run(Pipeline $pipeline, PipelineConfig $config, Directory $workingDirectory): PipelineHistory
    {
        $pipeline->addGlobalEvent(Pipeline::AFTER_EVENT, function(PipelineConfig $config, PipelineHistory $history, string $task) {
            $messages = $history->getMessages($task);
            foreach($messages->get('debug', []) as $message) {
                IO::writeln($message);
            }
        });
        return parent::run($pipeline, $config, $workingDirectory);
    }
}
