<?php

namespace OriginEngine\Commands\Pipelines;

use OriginEngine\Pipeline\Pipeline;
use OriginEngine\Pipeline\Tasks\Feature\ClearActiveFeature as ClearDefaultFeatureTask;

class ClearDefaultFeature extends Pipeline
{

    public function tasks(): array
    {
        return [
            'clear-default-feature' => new ClearDefaultFeatureTask()
        ];
    }
}