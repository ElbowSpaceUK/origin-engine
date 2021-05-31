<?php

namespace OriginEngine\Pipeline\Tasks\Feature;

use Illuminate\Support\Collection;
use OriginEngine\Contracts\Feature\FeatureResolver;
use OriginEngine\Feature\Feature;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Task;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Pipeline\TaskResponse;

class SetActiveFeature extends Task
{

    public function __construct(Feature $feature)
    {
        parent::__construct([
            'feature' => $feature
        ]);
    }

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        $feature = $config->get('feature');
        $this->writeInfo(sprintf('Setting active feature to ID %u', $feature->getId()));

        $featureResolver = app(FeatureResolver::class);
        $oldFeature = ($featureResolver->hasFeature($feature->getSite()) ? $featureResolver->getFeature($feature->getSite()) : null );
        $this->export('old-feature', $oldFeature);
        if($oldFeature === null) {
            $this->writeDebug('No feature is currently the active');
        } else {
            $this->writeDebug(sprintf('The active feature had an ID of %u', $oldFeature->getId()));
        }

        app(FeatureResolver::class)->setFeature($feature);

        return $this->succeeded();
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        $featureResolver = app(FeatureResolver::class);
        $feature = $config->get('old-feature', null);

        if($feature === null) {
            $featureResolver->clearFeature($feature->getSite());
        } else {
            app(FeatureResolver::class)->setFeature($config->get('old-feature'));
        }
    }

    protected function upName(Collection $config): string
    {
        return 'Changing active feature';
    }

    protected function downName(Collection $config): string
    {
        return 'Reverting active feature';
    }
}
