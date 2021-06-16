<?php

namespace OriginEngine\Plugins\Dependencies\Commands;

use OriginEngine\Contracts\Command\SiteCommand;
use OriginEngine\Feature\Feature;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Pipeline\RunsPipelines;
use OriginEngine\Plugins\Dependencies\Contracts\LocalPackageRepository;
use OriginEngine\Plugins\Dependencies\Pipelines\MakeDependencyLocal;
use OriginEngine\Plugins\Dependencies\LocalPackage;
use OriginEngine\Plugins\Dependencies\Pipelines\MakeExistingDependencyLocal;

class DepLocal extends SiteCommand
{
    use RunsPipelines;

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
    public function handle()
    {
        $site = $this->getSite('For which site should the dependency be installed locally?');

        if(!$site->hasCurrentFeature()) {
            throw new \Exception('No feature is currently active');
        }
        $feature = $site->getCurrentFeature();

        $workingDirectory = $feature->getDirectory();

        $package = $this->getOrAskForOption(
            'package',
            fn() => $this->ask('What package would you like to develop on locally?'),
            fn($value) => $value && is_string($value) && strlen($value) > 3 && LocalPackage::where(['name' => $value, 'feature_id' => $feature->getId()])->count() === 0
        );

        $dependency = $this->getDependencyWithName($package, $feature);

        if($dependency === null) {
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

            $history = $this->runPipeline(new MakeDependencyLocal(
                $package,
                $repositoryUrl,
                $feature,
                $branchName
            ), $workingDirectory);
        } else {
            $history = $this->runPipeline(new MakeExistingDependencyLocal($dependency), $workingDirectory);
        }

        if($history->allSuccessful()) {
            IO::success('Dependency installed locally');
        } else {
            IO::error('Dependency could not be installed locally');
        }

    }

    private function getDependencyWithName($name, Feature $feature): ?LocalPackage
    {
        foreach(app(LocalPackageRepository::class)->getAllThroughFeature($feature->getId()) as $localPackage) {
            if($name === $localPackage->getName()) {
                return $localPackage;
            }
        }
        return null;
    }
}
