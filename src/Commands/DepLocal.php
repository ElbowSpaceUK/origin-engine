<?php

namespace OriginEngine\Commands;

use OriginEngine\Contracts\Command\Command;
use OriginEngine\Contracts\Command\FeatureCommand;
use OriginEngine\Contracts\Feature\FeatureRepository;
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
use OriginEngine\Packages\LocalPackage;
use OriginEngine\Packages\LocalPackageHelper;
use OriginEngine\Site\Site;
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
    public function handle(SiteRepository $siteRepository, FeatureRepository $featureRepository)
    {
        $feature = $this->getFeature('Which feature should this be done against?');
        $site = $feature->getSite();

        $workingDirectory = $site->getDirectory();

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

        $localPackageFeature = $featureRepository->create(
            $site->getId(),
            sprintf('%s (%s)', $feature->getName(), $package),
            sprintf('%s (for %s)', $feature->getDescription(), $package),
            $feature->getType(),
            $branchName
        );

        $localPackage = LocalPackage::create([
            'name' => $package,
            'url' => $repositoryUrl,
            'type' => $this->getDependencyType($workingDirectory, $package),
            'original_version' => $this->getCurrentVersionConstraint($workingDirectory, $package),
            'feature_id' => $localPackageFeature->getId(),
            'parent_feature_id' => $feature->getId(),
        ]);

        $this->task('Storing project state', fn() => $localPackage);
        (new LocalPackageHelper())->makeLocal($localPackage, $workingDirectory);

        IO::success(sprintf('Module %s is now installed.', $package));
    }



    private function getDependencyType(Directory $workingDirectory, string $package): string
    {
        $reader = ComposerReader::for($workingDirectory);
        if($reader->isDependency($package, true)) {
            return 'direct';
        } elseif($reader->isInstalled($package)) {
            return 'indirect';
        }
        return 'none';
    }

    private function getCurrentVersionConstraint(Directory $workingDirectory, string $package, string $filename = 'composer.json'): ?string
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
