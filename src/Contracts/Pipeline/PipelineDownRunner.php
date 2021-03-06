<?php


namespace OriginEngine\Contracts\Pipeline;


use Illuminate\Support\Collection;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Pipeline;
use OriginEngine\Pipeline\PipelineConfig;
use OriginEngine\Pipeline\PipelineHistory;

interface PipelineDownRunner
{
    public function run(Pipeline $pipeline, Directory $workingDirectory, PipelineHistory $history, string $startFrom = null);
}
