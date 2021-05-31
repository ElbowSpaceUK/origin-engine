<?php

namespace OriginEngine\Pipeline\Tasks\Feature;

use Illuminate\Support\Collection;
use OriginEngine\Contracts\Feature\FeatureResolver;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Task;
use OriginEngine\Pipeline\TaskResponse;
use OriginEngine\Site\Site;

class ClearActiveFeature extends Task
{

    public function __construct(Site $site)
    {
        parent::__construct([
            'site' => $site
        ]);
    }

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        $this->writeInfo('Clearing the active feature');

        $featureResolver = app(FeatureResolver::class);
        $oldFeature = ($featureResolver->hasFeature($config->get('site')) ? $featureResolver->getFeature($config->get('site')) : null );
        $this->export('old-feature', $oldFeature);
        if($oldFeature === null) {
            $this->writeDebug('No feature is currently the active');
        } else {
            $this->writeDebug(sprintf('The active feature had an ID of %u', $oldFeature->getId()));
        }

        app(FeatureResolver::class)->clearFeature($config->get('site'));

        return $this->succeeded();
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        $featureResolver = app(FeatureResolver::class);
        $feature = $config->get('old-feature', null);

        if($feature === null) {
            $featureResolver->clearFeature($config->get('site'));
        } else {
            app(FeatureResolver::class)->setFeature($config->get('old-feature'));
        }
    }

    protected function upName(Collection $config): string
    {
        return 'Clearing active feature';
    }

    protected function downName(Collection $config): string
    {
        return 'Clearing active feature';
    }
}
