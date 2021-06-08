<?php

namespace OriginEngine\Plugins\Dependencies\Commands;

use OriginEngine\Contracts\Command\SiteCommand;
use OriginEngine\Contracts\Site\SiteRepository;
use OriginEngine\Feature\Feature;
use OriginEngine\Helpers\Composer\ComposerRunner;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\RunsPipelines;
use OriginEngine\Plugins\Dependencies\Pipelines\MakeDependencyRemote;
use OriginEngine\Plugins\Dependencies\LocalPackage;
use OriginEngine\Plugins\Dependencies\LocalPackageHelper;

class DepRemote extends SiteCommand
{
    use RunsPipelines;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'dep:remote
                            {--P|package= : The name of the local package}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Make a module a remote module';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(SiteRepository $siteRepository)
    {
        $site = $this->getSite('Which site do you want to use?');

        if(!$site->hasCurrentFeature()) {
            throw new \Exception('No feature is currently active');
        }
        $feature = $site->getCurrentFeature();

        $localPackage = LocalPackage::where([
            'name' => $this->getDependencyName($feature),
            'parent_feature_id' => $feature->getId()
        ])->firstOrFail();

        $workingDirectory = $site->getDirectory();

        IO::info(sprintf('Converting %s into a remote package.', $localPackage->getName()));

        $history = $this->runPipeline(
            new MakeDependencyRemote($localPackage),
            $workingDirectory
        );

        if($history->allSuccessful()) {
            IO::success(sprintf('Module %s has been made remote.', $localPackage->getName()));
        } else {
            IO::error(sprintf('Could not make module %s remote.', $localPackage->getName()));
        }
    }



    private function clearStaleDependencies(Directory $workingDirectory, string $package)
    {
        $currentVendorPath = Filesystem::append($workingDirectory->path(), 'vendor', $package);
        if(Filesystem::create()->exists($currentVendorPath)) {
            Filesystem::create()->remove($currentVendorPath);
        }
        return true;
    }

    private function updateComposer(Directory $workingDirectory)
    {
        ComposerRunner::for($workingDirectory)->update();
        return true;
    }

    private function getDependencyName(Feature $feature): string
    {
        $localPackages = LocalPackage::where('parent_feature_id', $feature->getId())->get();

        if(count($localPackages) === 0) {
            throw new \Exception('No dependencies are checked out for this site');
        }

        return $this->getOrAskForOption(
            'package',
            fn() => $this->choice(
                'Which dependency would you like to make local?',
                $localPackages->map(fn($package) => $package->getName())->toArray()
            ),
            fn($value) => $localPackages->filter(fn($package) => $package->getName() === $value)->count() > 0
        );
    }


}
