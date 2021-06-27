<?php

namespace OriginEngine\Pipeline\Runners;

use OriginEngine\Contracts\Pipeline\PipelineRunner as PipelineRunnerContract;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Pipeline;
use OriginEngine\Pipeline\PipelineConfig;
use OriginEngine\Pipeline\PipelineHistory;
use OriginEngine\Pipeline\Runners\VerbosePipelineRunner;

class VeryVerbosePipelineRunner extends VerbosePipelineRunner implements PipelineRunnerContract
{

    public function run(Pipeline $pipeline, PipelineConfig $config, Directory $workingDirectory): PipelineHistory
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

        $history = parent::run($pipeline, $config, $workingDirectory);

        dump($history);

        return $history;
    }
}
