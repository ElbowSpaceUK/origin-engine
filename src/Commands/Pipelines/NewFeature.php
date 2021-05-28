<?php

namespace OriginEngine\Commands\Pipelines;

use OriginEngine\Feature\Feature;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Pipeline\PipelineConfig;
use OriginEngine\Pipeline\PipelineHistory;
use OriginEngine\Pipeline\Tasks\Feature\CreateFeature;
use OriginEngine\Pipeline\Tasks\Utils\Closure;
use OriginEngine\Site\Site;
use OriginEngine\Pipeline\Pipeline;

class NewFeature extends Pipeline
{

    private Site $site;
    private string $featureName;
    private ?string $featureDescription;
    private string $featureType;
    private string $branchName;

    public function __construct(Site $site, string $featureName, ?string $featureDescription, string $featureType, string $branchName)
    {
        $this->site = $site;
        $this->featureName = $featureName;
        $this->featureDescription = $featureDescription;
        $this->featureType = $featureType;
        $this->branchName = $branchName;
    }

    public function tasks(): array
    {
        return [
            'create-feature' => new CreateFeature($this->site->getId(), $this->featureName, $this->featureDescription, $this->featureType, $this->branchName),
            'use-feature' => new Closure(function() {
                IO::warning('This should call the use feature pipeline once created');
            })
        ];
    }

    public function aliasedConfig(): array
    {
        return [
            'name' => 'create-feature.name',
            'description' => 'create-feature.description',
            'type' => 'create-feature.type',
            'branch' => 'create-feature.branch',
        ];
    }

}
