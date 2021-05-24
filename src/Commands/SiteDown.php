<?php

namespace OriginEngine\Commands;

use OriginEngine\Contracts\Command\Command;
use OriginEngine\Contracts\Command\SiteCommand;
use OriginEngine\Contracts\Pipeline\PipelineRunner;
use OriginEngine\Contracts\Site\SiteRepository;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Terminal\Executor;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Site\Site;

class SiteDown extends SiteCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'site:down';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Turn off the given site';

    protected bool $usePipelines = true;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(PipelineRunner $pipelineRunner, SiteRepository $siteRepository)
    {
        $site = $this->getSite(
            'Which site would you like to turn on?',
            $siteRepository->all()->filter(fn(Site $site) => $site->getStatus() === Site::STATUS_READY)->toArray()
        );

        IO::info('Turning off site.');

        $pipelineRunner->run($site->getBlueprint()->getSiteDownPipeline(), $this->getPipelineConfig(), $site->getDirectory());

        IO::success('Turned off site.');

    }

}
