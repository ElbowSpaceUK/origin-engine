<?php

namespace OriginEngine\Pipeline;

use OriginEngine\Contracts\Pipeline\PipelineRunner as PipelineRunnerContract;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;

class DebugPipelineRunner extends VeryVerbosePipelineRunner implements PipelineRunnerContract
{

    public function run(Pipeline $pipeline, PipelineConfig $config, WorkingDirectory $workingDirectory): PipelineHistory
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
