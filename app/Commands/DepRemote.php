<?php

namespace App\Commands;

use App\Core\Contracts\Command\Command;
use App\Core\Contracts\Command\FeatureCommand;
use App\Core\Contracts\Site\SiteRepository;
use App\Core\Feature\Feature;
use App\Core\Helpers\Composer\ComposerModifier;
use App\Core\Helpers\Composer\ComposerRunner;
use App\Core\Helpers\Composer\ComposerReader;
use App\Core\Helpers\Composer\Schema\ComposerRepository;
use App\Core\Helpers\Composer\Schema\Schema\ComposerSchema;
use App\Core\Helpers\IO\IO;
use App\Core\Helpers\Storage\Filesystem;
use App\Core\Helpers\WorkingDirectory\WorkingDirectory;
use App\Core\Packages\LocalPackage;
use App\Core\Packages\LocalPackageHelper;
use App\Core\Site\Site;
use Cz\Git\GitException;
use Cz\Git\GitRepository;
use Illuminate\Database\Eloquent\Collection;

class DepRemote extends FeatureCommand
{
    protected bool $supportsDependencies = false;

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

        $workingDirectory = WorkingDirectory::fromSite($site);

        IO::info(sprintf('Converting %s into a remote package.', $localPackage->getName()));


        (new LocalPackageHelper())->makeRemote($localPackage, $workingDirectory);
        $this->task('Updating project state', fn() => $localPackage->delete());

        IO::success(sprintf('Module %s has been made remote.', $localPackage->getName()));
    }



    private function clearStaleDependencies(WorkingDirectory $workingDirectory, string $package)
    {
        $currentVendorPath = Filesystem::append($workingDirectory->path(), 'vendor', $package);
        if(Filesystem::create()->exists($currentVendorPath)) {
            Filesystem::create()->remove($currentVendorPath);
        }
        return true;
    }

    private function updateComposer(WorkingDirectory $workingDirectory)
    {
        ComposerRunner::for($workingDirectory)->update();
        return true;
    }



}
