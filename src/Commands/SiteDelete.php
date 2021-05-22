<?php

namespace OriginEngine\Commands;

use OriginEngine\Contracts\Command\Command;
use OriginEngine\Contracts\Command\SiteCommand;
use OriginEngine\Contracts\Instance\InstanceRepository;
use OriginEngine\Contracts\Site\SiteRepository;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;
use OriginEngine\Pipeline\PipelineManager;

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

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(PipelineManager $installManager, SiteRepository $siteRepository, InstanceRepository $instanceRepository)
    {
        $site = $this->getSite('Which sites would you like to delete?', null, true);

        if(!$instanceRepository->exists($site->getInstanceId())) {
            IO::warning('The site was not found on the filesystem');
        } else {
            $installManager->driver($site->getInstaller())->uninstall(
                WorkingDirectory::fromSite($site)
            );
            IO::success('Removed the site from your filesystem');
        }

        $siteRepository->delete($site->getId());
        IO::success('Pruned remaining site data.');
    }

}
