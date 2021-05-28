<?php

namespace OriginEngine\Commands;

use OriginEngine\Commands\Pipelines\CheckoutFeature;
use OriginEngine\Contracts\Command\Command;
use OriginEngine\Contracts\Command\FeatureCommand;
use OriginEngine\Contracts\Feature\FeatureResolver;
use OriginEngine\Contracts\Site\SiteResolver;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\RunsPipelines;
use OriginEngine\Plugins\Dependencies\LocalPackage;
use OriginEngine\Plugins\Dependencies\LocalPackageHelper;
use Cz\Git\GitException;
use Cz\Git\GitRepository;
use OriginEngine\Plugins\Dependencies\Contracts\LocalPackageRepository;

class FeatureUse extends FeatureCommand
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
    public function handle(FeatureResolver $featureResolver, SiteResolver $siteResolver, LocalPackageHelper $localPackageHelper)
    {
        $feature = $this->getFeature('Which feature would you like to check out?');

        $this->runPipeline(new CheckoutFeature($feature), $feature->getSite()->getDirectory());
    }

}
