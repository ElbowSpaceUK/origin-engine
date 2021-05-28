<?php

namespace OriginEngine\Commands\Pipelines;

use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Pipeline\PipelineConfig;
use OriginEngine\Pipeline\PipelineHistory;
use OriginEngine\Pipeline\Tasks\DeleteFiles;
use OriginEngine\Pipeline\Tasks\Utils\RunPipeline;
use OriginEngine\Site\Site;
use OriginEngine\Pipeline\Pipeline;
use OriginEngine\Pipeline\Tasks\Site\DeleteSite as DeleteSiteTask;

class DeleteSite extends Pipeline
{

    private Site $site;

    public function __construct(Site $site)
    {
        $this->site = $site;
        $this->before('uninstall-site', function(PipelineConfig $config, PipelineHistory $history, string $task) {
            if(!Filesystem::create()->exists($this->site->getDirectory()->path())) {
                IO::warning('The site was not found on the filesystem, skipping uninstallation.');
            }
        });
    }

    public function tasks(): array
    {
        return [
            'uninstall-site' => new RunPipeline($this->site->getBlueprint()->getUninstallationPipeline()),
            'delete-site-from-database' => new DeleteSiteTask($this->site)
        ];
    }
}
