<?php

namespace OriginEngine\Commands\Feature;

use OriginEngine\Commands\Pipelines\CheckoutFeature;
use OriginEngine\Command\SiteCommand;
use OriginEngine\Contracts\Feature\FeatureResolver;
use OriginEngine\Contracts\Site\SiteResolver;
use OriginEngine\Pipeline\RunsPipelines;

class FeatureUse extends SiteCommand
{
    use RunsPipelines;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'feature:use';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Check out the feature.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(FeatureResolver $featureResolver, SiteResolver $siteResolver)
    {
        $feature = $this->getFeature('Which feature would you like to check out?');

        $this->runPipeline(new CheckoutFeature($feature), $feature->getSite()->getDirectory());
    }

}
