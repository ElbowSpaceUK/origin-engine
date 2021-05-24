<?php

namespace OriginEngine\Commands;

use OriginEngine\Contracts\Command\Command;
use OriginEngine\Contracts\Command\SiteCommand;
use OriginEngine\Contracts\Helpers\Directory\DirectoryValidator;
use OriginEngine\Contracts\Pipeline\PipelineRunner;
use OriginEngine\Contracts\Site\SiteRepository;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;
use OriginEngine\Pipeline\PipelineConfig;
use OriginEngine\Pipeline\PipelineManager;
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
     * @param DirectoryValidator $directoryValidator
     * @return void
     * @throws \Exception
     */
    public function handle(PipelineRunner $pipelineRunner, SiteRepository $siteRepository, DirectoryValidator $directoryValidator)
    {
        $site = $this->getSite('Which sites would you like to delete?');

        if(!$directoryValidator->isValid($site->getDirectory())) {
            IO::warning('The site was not found on the filesystem');
        } else {
            $pipelineRunner->run($site->getBlueprint()->getUninstallationPipeline(), $this->getPipelineConfig(), $site->getWorkingDirectory());
            IO::success('Removed the site from your filesystem');
        }

        $siteRepository->delete($site->getId());
        IO::success('Pruned remaining site data.');
    }

}
