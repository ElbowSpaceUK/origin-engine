<?php


namespace OriginEngine\Contracts\Pipeline;


use Illuminate\Support\Collection;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;
use OriginEngine\Pipeline\Pipeline;
use OriginEngine\Pipeline\PipelineConfig;
use OriginEngine\Pipeline\PipelineHistory;

interface PipelineRunner
{
    public function run(Pipeline $pipeline, PipelineConfig $config, WorkingDirectory $workingDirectory): PipelineHistory;
}
