<?php

namespace OriginEngine\Contracts\Command;

use OriginEngine\Contracts\Feature\FeatureRepository;
use OriginEngine\Contracts\Feature\FeatureResolver;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Feature\Feature;
use OriginEngine\Packages\LocalPackage;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

class FeatureCommand extends Command
{

    protected bool $supportsDependencies = true;

    private Feature $feature;

    private Directory $workingDirectory;

    private FeatureRepository $featureRepository;

    private FeatureResolver $featureResolver;

    static bool $confirmedFeature = false;

    public function configure()
    {
        parent::configure();
        $this->addOption('feature', 'F', InputOption::VALUE_OPTIONAL, 'The ID of the feature', null);
        if($this->supportsDependencies) {
            $this->addOption('dep', 'D', InputOption::VALUE_OPTIONAL, 'The name of the local dependency to run this on', null);
        }
    }

    /**
     * Get the feature the user wants to use
     *
     * @param string $message The message to show to the user if they're asked
     * @param \Closure|null $featureFilter A callback that takes a Feature instance and returns true or false as to whether the user can use it.
     * @param bool $ignoreDefault True will not use the default feature and instead always prompt the user for the feature.
     *
     * @return Feature
     * @throws \Exception If no features are available or the chosen feature could not be found
     */
    protected function getFeature(string $message = 'Which feature would you like to perform the action against?', ?array $features = null): Feature
    {
        if(isset($this->feature)) {
            return $this->feature;
        }

        if(empty($features) && !$this->featuresAreAvailable()) {
            throw new \Exception('No features are available');
        }

        if($features == null) {
            $features = $this->getAvailableFeatures();
        }

        // Get the feature from the default feature
        if($this->getFeatureResolver()->hasFeature() &&  (
                static::$confirmedFeature ||
                IO::confirm(sprintf('This will run on feature \'%s\', is this correct?', $this->getFeatureResolver()->getFeature()->getName()), true)
            )
        ) {
            $this->feature = $this->getFeatureResolver()->getFeature();
            static::$confirmedFeature = true;
            return $this->feature;
        }

        $featureId = $this->convertFeatureTextIntoId(
            $this->getOrAskForOption(
                'feature',
                fn() => $this->choice(
                    $message,
                    collect($features)->mapWithKeys(fn(Feature $feature) => [sprintf('feature-%u', $feature->getId()) => $feature->getName()])->toArray()
                ),
                fn($value) => $value && collect($features)->map(fn($feature) => $feature->getId())->contains($this->convertFeatureTextIntoId($value))
            )
        );

        $this->feature = $this->getFeatureRepository()->getById($featureId);

        return $this->feature;
    }

    private function featuresAreAvailable(): bool
    {
        return $this->getAvailableFeatures()->count() > 0;
    }

    private function getFeatureRepository(): FeatureRepository
    {
        if(!isset($this->featureRepository)) {
            $this->featureRepository = app(FeatureRepository::class);
        }
        return $this->featureRepository;
    }

    private function getAvailableFeatures(): Collection
    {
        return $this->getFeatureRepository()->all();
    }

    private function getFeatureResolver(): FeatureResolver
    {
        if(!isset($this->featureResolver)) {
            $this->featureResolver = app(FeatureResolver::class);
        }
        return $this->featureResolver;
    }

    private function cacheFeature(Feature $feature): Feature
    {
        $this->feature = $feature;
        return $this->feature;
    }

    private function convertFeatureTextIntoId(string $value): int
    {
        if(Str::startsWith($value, 'feature-')) {
            return (int) Str::substr($value, 8);
        }
        return (int) $value;
    }

    /**
     * @param string $message
     * @param \Closure|null $featureFilter
     * @param bool $ignoreDefault
     * @return Directory
     * @throws \Exception
     */
    public function getWorkingDirectory(string $message = 'Which feature would you like to perform the action against?'): Directory
    {
        if(!isset($this->workingDirectory)) {
            $feature = $this->getFeature($message, []);
            $paths = [$feature->getSite()->getDirectory()->path()];

            if($this->supportsDependencies && $this->option('dep') !== null) {
                $existingDeps = $feature->getLocalPackages()->filter(fn(LocalPackage $localPackage) => $localPackage->getName() === $this->option('dep'));
                if($existingDeps->count() > 0) {
                    $dep = $existingDeps->first();
                    $paths[] = sprintf('repos/%s', $dep->getName());
                } else {
                    throw new \Exception(sprintf('Dependency %s not found', $this->option('dep')));
                }
            }
            $this->workingDirectory = Directory::fromFullPath(Filesystem::append(...$paths));
        }
        return $this->workingDirectory;
    }

}
