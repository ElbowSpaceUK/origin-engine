<?php

namespace OriginEngine\Plugins\Dependencies\Tasks;

use Illuminate\Support\Collection;
use OriginEngine\Feature\Feature;
use OriginEngine\Helpers\Composer\ComposerReader;
use OriginEngine\Helpers\Composer\Schema\ComposerRepository;
use OriginEngine\Helpers\Composer\Schema\Schema\ComposerSchema;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Task;
use OriginEngine\Pipeline\TaskResponse;
use OriginEngine\Plugins\Dependencies\LocalPackage;

class CreateLocalDependencyModel extends Task
{

    public function __construct(string $name, string $url, Feature $dependencyFeature, Feature $siteFeature)
    {
        parent::__construct([
            'name' => $name,
            'url' => $url,
            'dependency-feature' => $dependencyFeature,
            'site-feature' => $siteFeature,
        ]);
    }

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        $localPackage = LocalPackage::create([
            'name' => $config->get('name'),
            'url' => $config->get('url'),
            'type' => $this->getDependencyType($workingDirectory, $config->get('name')),
            'original_version' => $this->getCurrentVersionConstraint($workingDirectory, $config->get('name')),
            'feature_id' => $config->get('dependency-feature')->getId(),
            'parent_feature_id' => $config->get('site-feature')->getId(),
        ]);

        $this->export('package', $localPackage);
        $this->writeInfo(sprintf('Created local package with ID %u', $localPackage->id));

        return $this->succeeded();
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        $output->get('package')->delete();
    }

    protected function upName(Collection $config): string
    {
        return 'Storing information about feature';
    }

    protected function downName(Collection $config): string
    {
        return 'Clearing information about feature';
    }

    protected function getDependencyType(Directory $workingDirectory, string $package): string
    {
        $reader = ComposerReader::for($workingDirectory);
        if($reader->isDependency($package, true)) {
            return 'direct';
        } elseif($reader->isInstalled($package)) {
            return 'indirect';
        }
        return 'none';
    }

    protected function getCurrentVersionConstraint(Directory $workingDirectory, string $package, string $filename = 'composer.json'): ?string
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
