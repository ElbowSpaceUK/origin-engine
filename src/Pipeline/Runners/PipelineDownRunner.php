<?php

namespace OriginEngine\Pipeline\Runners;

use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Pipeline;
use OriginEngine\Pipeline\PipelineHistory;

class PipelineDownRunner implements \OriginEngine\Contracts\Pipeline\PipelineDownRunner
{

    public function run(Pipeline $pipeline, Directory $workingDirectory, PipelineHistory $history, string $startFrom = null)
    {
        $tasks = array_reverse($pipeline->getTasks());

        $skip = true;
        foreach($tasks as $key => $task) {
            if($key === $startFrom) {
                $skip = false;
            }
            if($skip === true) {
                continue;
            }
            foreach($pipeline->getBeforeDownEvents($key) as $event) {
                $event($history, $key);
            }

            $task->reverse($workingDirectory, $history->succeeded($key), $history->getConfig($key), $history->getOutput($key));

            foreach($pipeline->getAfterDownEvents($key) as $event) {
                $event($history, $key);
            }
        }

    }

}
