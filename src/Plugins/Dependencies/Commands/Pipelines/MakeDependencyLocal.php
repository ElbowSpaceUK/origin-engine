<?php

namespace OriginEngine\Plugins\Dependencies\Pipelines;

use OriginEngine\Feature\Feature;
use OriginEngine\Pipeline\Pipeline;
use OriginEngine\Pipeline\PipelineConfig;
use OriginEngine\Pipeline\PipelineHistory;
use OriginEngine\Pipeline\Tasks\Utils\RunPipeline;
use OriginEngine\Plugins\Dependencies\LocalPackage;
use OriginEngine\Plugins\Dependencies\Tasks\CreateLocalDependencyModel;

class MakeDependencyLocal extends Pipeline
{

    private string $name;
    private string $url;
    private Feature $feature;
    private string $branch;

    private LocalPackage $localPackage;

    public function __construct(string $name, string $url, Feature $feature, string $branch)
    {
        $this->name = $name;
        $this->url = $url;
        $this->feature = $feature;
        $this->branch = $branch;

        $this->after('create-local-dependency-model', function(PipelineConfig $config, PipelineHistory $history, string $key) {
            $this->localPackage = $history->getOutput('create-local-dependency-model')->get('local-dependency');
        });
    }

    protected function tasks(): array
    {
        return [
            'create-local-dependency-model' => new CreateLocalDependencyModel($this->name, $this->url, $this->feature, $this->branch),
            'make-dependency-local' => fn() => new RunPipeline(new MakeExistingDependencyLocal($this->localPackage))
        ];
    }
}
