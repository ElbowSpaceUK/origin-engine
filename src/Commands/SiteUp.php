<?php

namespace OriginEngine\Commands;

use OriginEngine\Contracts\Command\Command;
use OriginEngine\Contracts\Command\SiteCommand;
use OriginEngine\Contracts\Pipeline\PipelineRunner;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Terminal\Executor;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;
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
    public function handle(PipelineRunner $pipelineRunner)
    {
        $site = $this->getSite(
            'Which site would you like to turn on?',
            fn(Site $site) => $site->getStatus() === Site::STATUS_DOWN
        );

        IO::info('Turning on site.');

        $pipelineRunner->run($site->getBlueprint()->getSiteUpPipeline(), $this->getPipelineConfig(), $site->getWorkingDirectory());

        IO::success('Turned on site.');

    }

}
