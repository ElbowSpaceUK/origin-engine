<?php

namespace OriginEngine\Commands\Pipelines;

use OriginEngine\Pipeline\Tasks\Site\SetDefaultSite as SetDefaultSiteTask;
use OriginEngine\Site\Site;
use OriginEngine\Pipeline\Pipeline;

class SetDefaultSite extends Pipeline
{

    private Site $site;

    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    public function tasks(): array
    {
        return [
            'set-default-site' => new SetDefaultSiteTask($this->site)
        ];
    }

}
