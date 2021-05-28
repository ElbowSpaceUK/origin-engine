<?php

namespace OriginEngine\Commands\Pipelines;

use OriginEngine\Pipeline\Pipeline;
use OriginEngine\Pipeline\Tasks\Site\ClearDefaultSite as ClearDefaultSiteTask;

class ClearDefaultSite extends Pipeline
{

    public function tasks(): array
    {
        return [
            'clear-default-site' => new ClearDefaultSiteTask()
        ];
    }
}
