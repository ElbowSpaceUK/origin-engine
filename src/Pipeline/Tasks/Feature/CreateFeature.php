<?php

namespace OriginEngine\Pipeline\Tasks\Feature;

use Illuminate\Support\Collection;
use OriginEngine\Contracts\Feature\FeatureRepository;
use OriginEngine\Contracts\Feature\FeatureResolver;
use OriginEngine\Feature\Feature;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Task;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Pipeline\TaskResponse;

class CreateFeature extends Task
{

    public function __construct(int $siteId, string $featureName, ?string $featureDescription, string $featureType, ?string $branchName = null)
    {
        parent::__construct([
            'site-id' => $siteId,
            'name' => $featureName,
            'description' => $featureDescription,
            'type' => $featureType,
            'branch' => $branchName
        ]);
    }

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        $feature = app(FeatureRepository::class)->create(
            $config->get('site-id'),
            $config->get('name'),
            $config->get('description'),
            $config->get('type'),
            $config->get('branch') ?? Feature::getDefaultBranchName($config->get('type'), $config->get('name')),
        );

        $this->writeSuccess(sprintf('Created feature %u', $feature->getId()));

        $this->export('feature', $feature);

        return $this->succeeded();
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        app(FeatureRepository::class)->delete($output->get('feature')->getId());
    }

    protected function upName(Collection $config): string
    {
        return 'Delete feature';
    }

    protected function downName(Collection $config): string
    {
        return 'Delete feature';
    }
}
