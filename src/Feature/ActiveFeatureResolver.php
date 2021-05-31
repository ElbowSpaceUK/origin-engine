<?php

namespace OriginEngine\Feature;

use OriginEngine\Contracts\Feature\FeatureRepository as FeatureRepositoryContract;
use OriginEngine\Contracts\Feature\FeatureResolver;
use OriginEngine\Site\Site;

class ActiveFeatureResolver implements FeatureResolver
{

    private FeatureRepositoryContract $featureRepository;

    public function __construct(FeatureRepositoryContract $featureRepository)
    {
        $this->featureRepository = $featureRepository;
    }

    public function setFeature(Feature $feature): void
    {
        $site = $feature->getSite()->getModel();
        $site->current_feature_id = $feature->getId();
        $site->save();
    }

    public function getFeature(Site $site): Feature
    {
        if($this->hasFeature($site)) {
            return $this->featureRepository->getById(
                $site->getModel()->getCurrentFeatureId()
            );
        }
        throw new \Exception('No feature is currently active');
    }

    public function hasFeature(Site $site): bool
    {
        return $site->getModel()->getCurrentFeatureId() !== null;
    }

    public function clearFeature(Site $site): void
    {
        if($this->hasFeature($site)) {
            $site = $site->getModel();
            $site->current_feature_id = null;
            $site->save();
        }
    }
}
