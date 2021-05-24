<?php

namespace OriginEngine\Pipeline;

use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Directory\Directory;

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
                $event($history);
            }

            IO::info(sprintf('Undoing Task: %s' , $task->getDownName($history->getConfig($key))));
            $task->reverse($workingDirectory, $history->succeeded($key), $history->getConfig($key), $history->getOutput($key));

            foreach($pipeline->getAfterDownEvents($key) as $event) {
                $event($history);
            }
        }

    }

}
