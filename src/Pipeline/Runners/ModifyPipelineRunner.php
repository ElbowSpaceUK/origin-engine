<?php

namespace OriginEngine\Pipeline\Runners;

use OriginEngine\Contracts\Pipeline\PipelineRunner as PipelineRunnerContract;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Pipeline;
use OriginEngine\Pipeline\PipelineConfig;
use OriginEngine\Pipeline\PipelineHistory;
use OriginEngine\Pipeline\PipelineModifier;
use function collect;

class ModifyPipelineRunner implements PipelineRunnerContract
{

    private PipelineRunnerContract $baseRunner;

    public function __construct(PipelineRunnerContract $baseRunner)
    {
        $this->baseRunner = $baseRunner;
    }

    public function run(Pipeline $pipeline, PipelineConfig $config, Directory $workingDirectory): PipelineHistory
    {
        $pipelineModifier = $this->getPipelineModifier();
        $pipelineModifier->modify($pipeline);
        return $this->baseRunner->run($pipeline, $config, $workingDirectory);
    }

    public function getPipelineModifier(): PipelineModifier
    {
        return app(PipelineModifier::class);
    }
}
