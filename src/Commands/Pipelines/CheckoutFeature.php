<?php

namespace OriginEngine\Commands\Pipelines;

use OriginEngine\Feature\Feature;
use OriginEngine\Pipeline\Pipeline;
use OriginEngine\Pipeline\Tasks\Git\CheckoutBranch;
use OriginEngine\Pipeline\Tasks\Utils\RunPipeline;

class CheckoutFeature extends Pipeline
{

    public Feature $feature;

    public function __construct(Feature $feature)
    {
        $this->feature = $feature;
    }

    public function tasks(): array
    {
        return [
            'reset-site' => new RunPipeline(new ResetSite($this->feature->getSite())),
            'checkout-feature-branch' => new CheckoutBranch($this->feature->getBranch(), true),
            'set-default-feature' => new \OriginEngine\Pipeline\Tasks\Feature\SetDefaultFeature($this->feature)
        ];
    }

}
