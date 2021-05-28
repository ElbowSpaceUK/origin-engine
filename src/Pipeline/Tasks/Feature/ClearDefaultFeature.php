<?php

namespace OriginEngine\Pipeline\Tasks\Feature;

use Illuminate\Support\Collection;
use OriginEngine\Contracts\Feature\FeatureResolver;
use OriginEngine\Feature\Feature;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Task;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Pipeline\TaskResponse;

class ClearDefaultFeature extends Task
{

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        $this->writeInfo('Clearing the default feature');

        $featureResolver = app(FeatureResolver::class);
        $oldFeature = ($featureResolver->hasFeature() ? $featureResolver->getFeature() : null );
        $this->export('old-feature', $oldFeature);
        if($oldFeature === null) {
            $this->writeDebug('No feature is currently the default');
        } else {
            $this->writeDebug(sprintf('The default feature had an ID of %u', $oldFeature->getId()));
        }

        app(FeatureResolver::class)->clearFeature();

        return $this->succeeded();
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        $featureResolver = app(FeatureResolver::class);
        $feature = $config->get('old-feature', null);

        if($feature === null) {
            $featureResolver->clearFeature();
        } else {
            app(FeatureResolver::class)->setFeature($config->get('old-feature'));
        }
    }

    protected function upName(Collection $config): string
    {
        return 'Clearing default feature';
    }

    protected function downName(Collection $config): string
    {
        return 'Clearing default feature';
    }
}
