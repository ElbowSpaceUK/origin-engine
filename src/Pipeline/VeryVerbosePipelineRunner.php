<?php

namespace OriginEngine\Pipeline;

use OriginEngine\Contracts\Pipeline\PipelineRunner as PipelineRunnerContract;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;

class VeryVerbosePipelineRunner extends VerbosePipelineRunner implements PipelineRunnerContract
{

    public function run(Pipeline $pipeline, PipelineConfig $config, WorkingDirectory $workingDirectory): PipelineHistory
    {
        $pipeline->addGlobalEvent(Pipeline::AFTER_EVENT, function(PipelineConfig $config, PipelineHistory $history, string $task) {
            $messages = $history->getMessages($task);

            foreach($messages->get('info', []) as $message) {
                IO::info($message);
            }
            foreach($messages->get('warning', []) as $message) {
                IO::warning($message);
            }
            foreach($messages->get('error', []) as $message) {
                IO::error($message);
            }
            foreach($messages->get('success', []) as $message) {
                IO::success($message);
            }
        });
        return parent::run($pipeline, $config, $workingDirectory);
    }
}
