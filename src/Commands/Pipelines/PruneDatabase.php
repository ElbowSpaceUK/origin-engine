<?php

namespace OriginEngine\Commands\Pipelines;

use OriginEngine\Pipeline\Pipeline;
use OriginEngine\Pipeline\Task;
use OriginEngine\Pipeline\Tasks\Utils\Repeater;
use OriginEngine\Site\Site;

class PruneDatabase extends Pipeline
{
    /**
     * @var array|Site[]
     */
    private array $sites;

    /**
     * PruneDatabase constructor.
     * @param array|Site[] $sites
     */
    public function __construct(array $sites)
    {
        $this->sites = $sites;
    }

    public function tasks(): array
    {
        $tasks = [];
        foreach(collect($this->sites)->filter(fn(Site $site) => $site->getStatus() === Site::STATUS_MISSING)->toArray() as $site) {
            $tasks[sprintf('remove-site-missing-files-%u', $site->getId())] = new \OriginEngine\Pipeline\Tasks\Site\DeleteSite($site);
        }
        return $tasks;
    }
}
