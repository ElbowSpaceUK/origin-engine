<?php

namespace OriginEngine\Plugins\Dependencies\Commands;

use OriginEngine\Contracts\Command\Command;
use OriginEngine\Contracts\Command\FeatureCommand;
use OriginEngine\Contracts\Site\SiteRepository;
use OriginEngine\Feature\Feature;
use OriginEngine\Helpers\Composer\ComposerModifier;
use OriginEngine\Helpers\Composer\ComposerRunner;
use OriginEngine\Helpers\Composer\ComposerReader;
use OriginEngine\Helpers\Composer\Schema\ComposerRepository;
use OriginEngine\Helpers\Composer\Schema\Schema\ComposerSchema;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Plugins\Dependencies\LocalPackage;
use OriginEngine\Plugins\Dependencies\LocalPackageHelper;
use OriginEngine\Site\Site;
use Cz\Git\GitException;
use Cz\Git\GitRepository;
use Illuminate\Database\Eloquent\Collection;

class DepRemote extends FeatureCommand
{

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
        $feature = $this->getFeature('Which feature do you want to use?');
        $site = $feature->getSite();
        /** @var LocalPackage[]|Collection $localPackages */
        $localPackages = LocalPackage::where('feature_id', $feature->getId())->get();

        /** @var LocalPackage $localPackage */
        $localPackage = LocalPackage::where([
            'name' => $this->getOrAskForOption(
                'package',
                fn() => $this->choice(
                    'Which dependency would you like to make local?',
                    $localPackages->map(fn($package) => $package->getName())->toArray()
                ),
                fn($value) => $localPackages->filter(fn($package) => $package->getName() === $value)->count() > 0
            ),
            'feature_id' => $feature->getId()
        ])->firstOrFail();

        $workingDirectory = $site->getDirectory();

        IO::info(sprintf('Converting %s into a remote package.', $localPackage->getName()));


        (new LocalPackageHelper())->makeRemote($localPackage, $workingDirectory);
        $this->task('Updating project state', fn() => $localPackage->delete());

        IO::success(sprintf('Module %s has been made remote.', $localPackage->getName()));
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



}
