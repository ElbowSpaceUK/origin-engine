<?php

namespace OriginEngine\Commands\Pipelines;

use OriginEngine\Feature\Feature;
use OriginEngine\Pipeline\Pipeline;
use OriginEngine\Pipeline\Tasks\Feature\SetDefaultFeature;

class FeatureDefault extends Pipeline
{

    private Feature $feature;

    public function __construct(Feature $feature)
    {
        $this->feature = $feature;
    }

    public function getTasks(): array
    {
        return [
            'set-default-feature' => new SetDefaultFeature($this->feature)
        ];
    }
}
