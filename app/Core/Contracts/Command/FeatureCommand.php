<?php

namespace App\Core\Contracts\Command;

use App\Core\Contracts\Feature\FeatureRepository;
use App\Core\Contracts\Feature\FeatureResolver;
use App\Core\Helpers\IO\IO;
use App\Core\Helpers\Storage\Filesystem;
use App\Core\Helpers\WorkingDirectory\WorkingDirectory;
use App\Core\Feature\Feature;
use App\Core\Packages\LocalPackage;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

class FeatureCommand extends Command
{

    protected bool $supportsDependencies = true;

    private Feature $feature;

    private WorkingDirectory $workingDirectory;

    private FeatureRepository $featureRepository;

    private FeatureResolver $featureResolver;
    private \Illuminate\Database\Eloquent\Collection $allFeatures;

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
    protected function getFeature(string $message = 'Which feature would you like to perform the action against?',
                               \Closure $featureFilter = null,
                               bool $ignoreDefault = false): Feature
    {
        if(isset($this->feature)) {
            return $this->feature;
        }

        if(!$this->featuresAreAvailable($featureFilter)) {
            throw new \Exception('No features are available');
        }

        // Get the feature from the default feature
        if($ignoreDefault === false && $this->getFeatureResolver()->hasFeature()) {
            return $this->cacheFeature($this->getFeatureResolver()->getFeature());
        }

        $featureId = $this->convertFeatureTextIntoId(
            $this->getOrAskForOption(
                'feature',
                fn() => $this->choice(
                    $message,
                    $this->getAvailableFeatures($featureFilter)->mapWithKeys(fn(Feature $feature) => [sprintf('feature-%u', $feature->getId()) => $feature->getName()])->toArray()
                ),
                fn($value) => $value && $this->getAvailableFeatures($featureFilter)->map(fn($feature) => $feature->getId())->contains($this->convertFeatureTextIntoId($value))
            )
        );

        $feature = $this->getFeatureRepository()->getById($featureId);

        $this->feature = $feature;
        return $this->feature;
    }

    private function promptUserForFeature(string $message, ?\Closure $featureFilter): int
    {
        $prefixedFeatureId = $this->choice(
            $message,
            $this->getAvailableFeatures($featureFilter)->mapWithKeys(fn(Feature $feature) => [sprintf('feature-%u', $feature->getId()) => $feature->getName()])->toArray()
        );

        if($prefixedFeatureId && $this->getAvailableFeatures($featureFilter)->map(fn($feature) => $feature->getId())->contains($this->convertFeatureTextIntoId($prefixedFeatureId))) {
            IO::error(sprintf('[%s] is not a valid feature', $prefixedFeatureId));
            return $this->promptUserForFeature($message, $featureFilter);
        }

        return $this->convertFeatureTextIntoId($prefixedFeatureId);
    }

    private function featuresAreAvailable(?\Closure $featureFilter): bool
    {
        return $this->getAvailableFeatures($featureFilter)->count() > 0;
    }

    private function getFeatureRepository(): FeatureRepository
    {
        if(!isset($this->featureRepository)) {
            $this->featureRepository = app(FeatureRepository::class);
        }
        return $this->featureRepository;
    }

    private function getAvailableFeatures(?\Closure $featureFilter = null): Collection
    {
        if(!isset($this->allFeatures)) {
            $this->allFeatures = $this->getFeatureRepository()->all()->filter($featureFilter);
        }

        return $this->allFeatures;
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
     * @return WorkingDirectory
     * @throws \Exception
     */
    public function getWorkingDirectory(string $message = 'Which feature would you like to perform the action against?',
                                        \Closure $featureFilter = null,
                                        bool $ignoreDefault = false): WorkingDirectory
    {
        if(!isset($this->workingDirectory)) {
            $feature = $this->getFeature($message, $featureFilter, $ignoreDefault);
            $paths = [$feature->getSite()->getWorkingDirectory()->path()];

            if($this->supportsDependencies && $this->option('dep') !== null) {
                $existingDeps = $feature->getLocalPackages()->filter(fn(LocalPackage $localPackage) => $localPackage->getName() === $this->option('dep'));
                if($existingDeps->count() > 0) {
                    $dep = $existingDeps->first();
                    $paths[] = sprintf('repos/%s', $dep->getName());
                } else {
                    throw new \Exception(sprintf('Dependency %s not found', $this->option('dep')));
                }
            }
            $this->workingDirectory = WorkingDirectory::fromPath(Filesystem::append(...$paths));
        }
        return $this->workingDirectory;
    }

}
