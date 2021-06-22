<?php

namespace OriginEngine\Commands\Site;

use OriginEngine\Contracts\Command\SiteCommand;
use OriginEngine\Contracts\Pipeline\PipelineRunner;
use OriginEngine\Contracts\Site\SiteRepository;
use OriginEngine\Pipeline\RunsPipelines;
use OriginEngine\Site\Site;

class SiteDown extends SiteCommand
{
    use RunsPipelines;

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

        $this->runPipeline($site->getBlueprint()->getSiteDownPipeline(), $site->getDirectory());

    }

}
