<?php

namespace OriginEngine\Plugins\Dependencies\Commands\Pipelines;

use OriginEngine\Feature\Feature;
use OriginEngine\Pipeline\Pipeline;
use OriginEngine\Pipeline\PipelineConfig;
use OriginEngine\Pipeline\PipelineHistory;
use OriginEngine\Pipeline\Tasks\Feature\CreateFeature;
use OriginEngine\Pipeline\Tasks\Utils\Closure;
use OriginEngine\Pipeline\Tasks\Utils\CreateAndRunTask;
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

    private Feature $dependencyFeature;

    public function __construct(string $name, string $url, Feature $feature, string $branch)
    {
        $this->name = $name;
        $this->url = $url;
        $this->feature = $feature;
        $this->branch = $branch;

        $this->after('create-local-dependency-model', function(PipelineConfig $config, PipelineHistory $history, string $key) {
            $this->localPackage = $history->getOutput($key)->get('package');
        });
        $this->after('create-feature-model', function(PipelineConfig $config, PipelineHistory $history, string $key) {
            $this->dependencyFeature = $history->getOutput($key)->get('feature');
        });
    }

    protected function tasks(): array
    {
        return [
            'create-feature-model' => new CreateFeature(
                $this->feature->getSite()->getId(),
                sprintf('%s (%s)', $this->feature->getName(), $this->name),
                sprintf('%s (for %s)', $this->feature->getDescription(), $this->name),
                $this->feature->getType(),
                $this->branch
            ),
            'create-local-dependency-model' => new CreateAndRunTask(
                fn() => new CreateLocalDependencyModel($this->name, $this->url, $this->dependencyFeature, $this->feature),
                [],
                'Store the status of the dependency',
                'Clear the status of the dependency'
            ),
            'make-dependency-local' => new CreateAndRunTask(
                fn() => new RunPipeline(new MakeExistingDependencyLocal($this->localPackage)),
                [],
                'Make the dependency local',
                'Make the dependency remote'
            )
        ];
    }
}
