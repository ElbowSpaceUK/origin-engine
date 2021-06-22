<?php

namespace OriginEngine\Commands\Site;

use OriginEngine\Commands\Pipelines\DeleteSite;
use OriginEngine\Contracts\Command\Command;
use OriginEngine\Contracts\Command\SiteCommand;
use OriginEngine\Contracts\Pipeline\PipelineRunner;
use OriginEngine\Contracts\Site\SiteRepository;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Pipeline\PipelineConfig;
use OriginEngine\Pipeline\PipelineModifier;
use OriginEngine\Pipeline\RunsPipelines;
use OriginEngine\Site\Site;

class SiteDelete extends SiteCommand
{
    use RunsPipelines;

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
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $site = $this->getSite('Which sites would you like to delete?');

        $this->runPipeline(new DeleteSite($site), $site->getDirectory());
    }

}
