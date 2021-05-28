<?php

namespace OriginEngine\Pipeline\Tasks\Site;

use Illuminate\Support\Collection;
use OriginEngine\Contracts\Site\SiteRepository;
use OriginEngine\Contracts\Site\SiteResolver;
use OriginEngine\Site\Site;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Task;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Pipeline\TaskResponse;

class DeleteSite extends Task
{

    public function __construct(Site $site)
    {
        parent::__construct([
            'site' => $site
        ]);
    }

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        /** @var Site $site */
        $site = $config->get('site');

        $this->writeInfo(sprintf('Deleting site %s', $site->getId()));

        $this->export('site-id', $site->getId());

        app(SiteRepository::class)->delete(
            $config->get('site')->getId()
        );

        return $this->succeeded();
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        app(SiteRepository::class)->restore($output->get('site-id'));
    }

    protected function upName(Collection $config): string
    {
        return 'Delete site';
    }

    protected function downName(Collection $config): string
    {
        return 'Delete site';
    }
}
