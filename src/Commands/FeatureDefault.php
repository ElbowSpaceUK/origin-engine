<?php

namespace OriginEngine\Commands;

use OriginEngine\Contracts\Command\Command;
use OriginEngine\Contracts\Command\FeatureCommand;
use OriginEngine\Contracts\Feature\FeatureResolver;
use OriginEngine\Contracts\Site\SiteResolver;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Packages\LocalPackage;
use OriginEngine\Packages\LocalPackageHelper;
use Cz\Git\GitException;
use Cz\Git\GitRepository;

class FeatureDefault extends FeatureCommand
{
    protected bool $supportsDependencies = false;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'feature:default';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Use the given feature by default.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(FeatureResolver $featureResolver, SiteResolver $siteResolver, LocalPackageHelper $localPackageHelper)
    {
        $feature = $this->getFeature('Which feature would you like to use by default?');

        $this->task(sprintf('Setting the default feature to %s', $feature->getName()), fn() => $featureResolver->setFeature($feature));

    }

}
