<?php

namespace OriginEngine\Commands;

use OriginEngine\Contracts\Command\Command;
use OriginEngine\Contracts\Command\FeatureCommand;
use OriginEngine\Contracts\Feature\FeatureResolver;
use OriginEngine\Contracts\Site\SiteResolver;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;
use OriginEngine\Packages\LocalPackage;
use OriginEngine\Packages\LocalPackageHelper;
use Cz\Git\GitException;
use Cz\Git\GitRepository;

class FeatureUse extends FeatureCommand
{
    protected bool $supportsDependencies = false;

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

        IO::info('Switching feature to ' . $feature->getName() . '.');

        $this->task('Resetting the site',
            fn() => $this->call(SiteReset::class, ['--site' => $feature->getSite()->getId()]));

        $workingDirectory = WorkingDirectory::fromSite($feature->getSite());

        // TODO Base branch stored in site definition
        $this->task('Checkout the base branch', function() use ($feature) {
            $git = new GitRepository($feature->getSite()->getWorkingDirectory()->path());
            try {
                $git->checkout($feature->getBranch());
            } catch (GitException $e) {
                $git->createBranch($feature->getBranch(), true);
            }
        });

        $this->task('Install local packages', function() use ($feature, $localPackageHelper, $workingDirectory) {
            /** @var LocalPackage[] $packages */
            $packages = $feature->getLocalPackages();

            foreach($packages as $package) {
                $localPackageHelper->makeLocal($package, $workingDirectory);
            }
        });

    }

}
