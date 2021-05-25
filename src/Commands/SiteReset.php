<?php

namespace OriginEngine\Commands;

use OriginEngine\Contracts\Command\Command;
use OriginEngine\Contracts\Command\SiteCommand;
use OriginEngine\Contracts\Feature\FeatureResolver;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Plugins\Dependencies\LocalPackage;
use OriginEngine\Plugins\Dependencies\LocalPackageHelper;
use Cz\Git\GitException;
use Cz\Git\GitRepository;
use OriginEngine\Plugins\Dependencies\Contracts\LocalPackageRepository;

class SiteReset extends SiteCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'site:reset
                            {--B|branch= : The name of the branch to check out}';

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
    public function handle(FeatureResolver $featureResolver, LocalPackageHelper $localPackageHelper)
    {
        $site = $this->getSite('Which site would you like to reset?');
        $branch = $this->getOrAskForOption(
            'branch',
            fn() => 'remove-module-installer',//$this->ask('What branch would you like to reset to?', 'develop'),
            fn($value) => $value && strlen($value) > 0
        );

        $feature = $site->getCurrentFeature();
        $workingDirectory = $site->getDirectory();

        if($feature !== null) {
            // Site has a feature currently checked out
            $packages = app(LocalPackageRepository::class)->getAllThroughFeature($feature->getId());
            if(count($packages) > 0) {
                IO::progressStart($packages->count());
                foreach($packages as $package) {
                    $localPackageHelper->makeRemote($package, $workingDirectory);
                    IO::progressStep(1);
                }
                IO::progressFinish();
            }
        }

        $git = new GitRepository($site->getDirectory()->path());
        try {
            $git->checkout($branch);
        } catch (GitException $e) {
            $git->createBranch($branch, true);
        }

        $featureResolver->clearFeature();

    }

}
