<?php

namespace App\Commands;

use App\Core\Contracts\Command\Command;
use App\Core\Contracts\Command\FeatureCommand;
use App\Core\Contracts\Feature\FeatureResolver;
use App\Core\Contracts\Site\SiteResolver;
use App\Core\Helpers\IO\IO;
use App\Core\Helpers\WorkingDirectory\WorkingDirectory;
use App\Core\Packages\LocalPackage;
use App\Core\Packages\LocalPackageHelper;
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
    protected $description = 'Use the given feature by default.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(FeatureResolver $featureResolver, SiteResolver $siteResolver, LocalPackageHelper $localPackageHelper)
    {
        $feature = $this->getFeature('Which feature would you like to use by default?', null, true);

        IO::info('Switching default feature to ' . $feature->getName() . '.');

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

        $this->task(sprintf('Setting the default feature to %s', $feature->getName()), fn() => $featureResolver->setFeature($feature));

    }

}
