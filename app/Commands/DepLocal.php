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

class DepLocal extends FeatureCommand
{
    protected bool $supportsDependencies = false;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'dep:local
                            {--P|package= : The composer package name}
                            {--B|branch= : A name for the branch to use}
                            {--R|repository-url= : The URL of the repository}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Make a module a local module';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(SiteRepository $siteRepository)
    {
        $feature = $this->getFeature('Which feature should this be done against?');
        $site = $feature->getSite();

        $workingDirectory = WorkingDirectory::fromSite($site);

        $package = $this->getOrAskForOption(
            'package',
            fn() => $this->ask('What package would you like to develop on locally?'),
            fn($value) => $value && is_string($value) && strlen($value) > 3 && LocalPackage::where(['name' => $value, 'feature_id' => $feature->getId()])->count() === 0
        );

        $repositoryUrl = $this->getOrAskForOption(
            'repository-url',
            fn() => $this->ask('What is the git URL of the package repository?', sprintf('git@github.com:%s', $package)),
            fn($value) => $value && is_string($value) && strlen($value) > 3
        );

        $branchName = $this->getOrAskForOption(
            'branch',
            fn() => $this->ask('What should we name the branch?', $feature->getBranch()),
            fn($value) => $value && strlen($value) > 0
        );

        IO::info(sprintf('Converting %s into a local package.', $package));

        $localPackage = LocalPackage::create([
            'name' => $package,
            'url' => $repositoryUrl,
            'type' => $this->getDependencyType($workingDirectory, $package),
            'original_version' => $this->getCurrentVersionConstraint($workingDirectory, $package),
            'feature_id' => $feature->getId(),
            'branch' => $branchName
        ]);

        $this->task('Storing project state', fn() => $localPackage);
        (new LocalPackageHelper())->makeLocal($localPackage, $workingDirectory);

        IO::success(sprintf('Module %s is now installed.', $package));
    }



    private function getDependencyType(WorkingDirectory $workingDirectory, string $package): string
    {
        $reader = ComposerReader::for($workingDirectory);
        if($reader->isDependency($package, true)) {
            return 'direct';
        } elseif($reader->isInstalled($package)) {
            return 'indirect';
        }
        return 'none';
    }

    private function getCurrentVersionConstraint(WorkingDirectory $workingDirectory, string $package, string $filename = 'composer.json'): ?string
    {
        /** @var ComposerSchema $composer */
        $composer = app(ComposerRepository::class)->get($workingDirectory, $filename);
        foreach($composer->getRequire() as $packageSchema) {
            if($packageSchema->getName() === $package) {
                return $packageSchema->getVersion();
            }
        }
        foreach($composer->getRequireDev() as $packageSchema) {
            if($packageSchema->getName() === $package) {
                return $packageSchema->getVersion();
            }
        }
        return null;
    }

}
