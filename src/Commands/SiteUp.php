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

class SiteUp extends SiteCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'site:up';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Turn on the given site';

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
            $siteRepository->all()->filter(fn(Site $site) => $site->getStatus() === Site::STATUS_DOWN)->toArray()
        );

        IO::info('Turning on site.');

        $history = $pipelineRunner->run($site->getBlueprint()->getSiteUpPipeline(), $this->getPipelineConfig(), $site->getDirectory());

        IO::success('Turned on site.');

    }

}
