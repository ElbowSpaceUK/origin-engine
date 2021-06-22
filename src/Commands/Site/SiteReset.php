<?php

namespace OriginEngine\Commands\Site;

use OriginEngine\Commands\Pipelines\ResetSite;
use OriginEngine\Contracts\Command\SiteCommand;
use OriginEngine\Contracts\Feature\FeatureResolver;
use OriginEngine\Pipeline\RunsPipelines;

class SiteReset extends SiteCommand
{
    use RunsPipelines;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'site:reset';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Reset a site back to its starting point, as if it were a fresh install.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(FeatureResolver $featureResolver)
    {
        $site = $this->getSite('Which site would you like to reset?');

        $this->runPipeline(new ResetSite($site), $site->getDirectory());
    }

}
