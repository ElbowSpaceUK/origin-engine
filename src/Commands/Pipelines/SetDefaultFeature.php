<?php

namespace OriginEngine\Commands\Pipelines;

use OriginEngine\Feature\Feature;
use OriginEngine\Pipeline\Pipeline;
use OriginEngine\Pipeline\Tasks\Feature\SetActiveFeature as SetDefaultFeatureTask;

class SetDefaultFeature extends Pipeline
{

    private Feature $feature;

    public function __construct(Feature $feature)
    {
        $this->feature = $feature;
    }

    public function tasks(): array
    {
        return [
            'set-default-feature' => new SetDefaultFeatureTask($this->feature)
        ];
    }

}
