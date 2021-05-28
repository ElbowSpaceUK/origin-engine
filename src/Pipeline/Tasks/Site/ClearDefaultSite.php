<?php

namespace OriginEngine\Pipeline\Tasks\Site;

use Illuminate\Support\Collection;
use OriginEngine\Contracts\Site\SiteResolver;
use OriginEngine\Site\Site;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Task;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Pipeline\TaskResponse;

class ClearDefaultSite extends Task
{

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        $this->writeInfo('Clearing the default site');

        $siteResolver = app(SiteResolver::class);
        $oldSite = ($siteResolver->hasSite() ? $siteResolver->getSite() : null );
        $this->export('old-site', $oldSite);
        if($oldSite === null) {
            $this->writeDebug('No site is currently the default');
        } else {
            $this->writeDebug(sprintf('The default site had an ID of %u', $oldSite->getId()));
        }

        app(SiteResolver::class)->clearSite();

        return $this->succeeded();
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        $siteResolver = app(SiteResolver::class);
        $site = $config->get('old-site', null);

        if($site === null) {
            $siteResolver->clearSite();
        } else {
            app(SiteResolver::class)->setSite($config->get('old-site'));
        }
    }

    protected function upName(Collection $config): string
    {
        return 'Clearing default site';
    }

    protected function downName(Collection $config): string
    {
        return 'Clearing default site';
    }
}
