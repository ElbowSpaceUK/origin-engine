<?php

namespace OriginEngine\Commands;

use OriginEngine\Contracts\Command\Command;
use OriginEngine\Contracts\Command\SiteCommand;
use OriginEngine\Contracts\Pipeline\PipelineRunner;
use OriginEngine\Contracts\Site\SiteRepository;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Pipeline\PipelineConfig;
use OriginEngine\Pipeline\PipelineModifier;
use OriginEngine\Site\Site;

class SiteDelete extends SiteCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'site:delete';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Delete the given site';

    protected bool $usePipelines = true;

    /**
     * Execute the console command.
     *
     * @param PipelineRunner $pipelineRunner
     * @param SiteRepository $siteRepository
     * @return void
     * @throws \Exception
     */
    public function handle(PipelineRunner $pipelineRunner, SiteRepository $siteRepository)
    {
        $site = $this->getSite('Which sites would you like to delete?');

        if(!$this->directoryExists($site)) {
            IO::warning('The site was not found on the filesystem');
        } else {
            $pipelineRunner->run($site->getBlueprint()->getUninstallationPipeline(), $this->getPipelineConfig(), $site->getDirectory());
            IO::success('Removed the site from your filesystem');
        }

        $siteRepository->delete($site->getId());
        IO::success('Pruned remaining site data.');
    }

    public function directoryExists(Site $site)
    {
        return Filesystem::create()->exists(
            $site->getDirectory()->path()
        );
    }

}
