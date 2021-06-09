<?php


namespace OriginEngine\Contracts\Pipeline;


use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Pipeline;
use OriginEngine\Pipeline\PipelineConfig;
use OriginEngine\Pipeline\PipelineHistory;

interface PipelineRunner
{
    public function run(Pipeline $pipeline, PipelineConfig $config, Directory $workingDirectory): PipelineHistory;
}
