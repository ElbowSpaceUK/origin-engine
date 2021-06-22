<?php

namespace OriginEngine\Commands\Pipelines;

use OriginEngine\Feature\Feature;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Pipeline\Pipeline;
use OriginEngine\Pipeline\PipelineConfig;
use OriginEngine\Pipeline\PipelineHistory;
use OriginEngine\Pipeline\Tasks\Feature\DeleteFeature as DeleteFeatureTask;
use OriginEngine\Pipeline\Tasks\Utils\RunPipeline;

class DeleteFeature extends Pipeline
{

    private Feature $feature;

    public function __construct(Feature $feature)
    {
        $this->feature = $feature;
        $this->before('reset-site', function(PipelineConfig $config, PipelineHistory $history, string $key) {
            if( // the feature being deleted is the current feature
                $this->feature->getSite()->hasCurrentFeature() &&
                $this->feature->getSite()->getCurrentFeature()->getId() === $this->feature->getId()
            ) {
                return false;
            }
        });

    }

    public function tasks(): array
    {
        return [
            'reset-site' => new RunPipeline(new ResetSite($this->feature->getSite())),
            'delete-feature' => new DeleteFeatureTask($this->feature)
        ];
    }
}
