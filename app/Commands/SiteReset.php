<?php

namespace App\Commands;

use App\Core\Contracts\Command\Command;
use App\Core\Contracts\Command\SiteCommand;
use App\Core\Contracts\Feature\FeatureResolver;
use App\Core\Helpers\IO\IO;
use App\Core\Helpers\WorkingDirectory\WorkingDirectory;
use App\Core\Packages\LocalPackage;
use App\Core\Packages\LocalPackageHelper;
use Cz\Git\GitException;
use Cz\Git\GitRepository;

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
        $site = $this->getSite('Which site would you like to reset?', null, true);
        $branch = $this->getOrAskForOption(
            'branch',
            fn() => 'remove-module-installer',//$this->ask('What branch would you like to reset to?', 'develop'),
            fn($value) => $value && strlen($value) > 0
        );

        $feature = $site->getCurrentFeature();
        $workingDirectory = WorkingDirectory::fromSite($site);

        if($feature !== null) {
            // Site has a feature currently checked out
            $packages = $feature->getLocalPackages();
            if(count($packages) > 0) {
                IO::progressStart($packages->count());
                foreach($packages as $package) {
                    $localPackageHelper->makeRemote($package, $workingDirectory);
                    IO::progressStep(1);
                }
                IO::progressFinish();
            }
        }

        $git = new GitRepository($site->getWorkingDirectory()->path());
        try {
            $git->checkout($branch);
        } catch (GitException $e) {
            $git->createBranch($branch, true);
        }

        $featureResolver->clearFeature();

    }

}
