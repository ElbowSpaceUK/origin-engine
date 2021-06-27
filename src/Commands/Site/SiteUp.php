<?php

namespace OriginEngine\Commands\Site;

use OriginEngine\Command\Command;
use OriginEngine\Command\SiteCommand;
use OriginEngine\Contracts\Pipeline\PipelineRunner;
use OriginEngine\Contracts\Site\SiteRepository;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Terminal\Executor;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\RunsPipelines;
use OriginEngine\Site\Site;

class SiteUp extends SiteCommand
{
    use RunsPipelines;

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
    public function handle(SiteRepository $siteRepository)
    {
        $site = $this->getSite(
            'Which site would you like to turn on?',
            $siteRepository->all()->filter(fn(Site $site) => $site->getStatus() === Site::STATUS_DOWN)->toArray()
        );
        $history = $this->runPipeline($site->getBlueprint()->getSiteUpPipeline(), $site->getDirectory());

        IO::success('Turned on site.');
    }

}
