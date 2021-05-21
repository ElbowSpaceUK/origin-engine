<?php

namespace App\Core\Feature;

use App\Core\Contracts\Feature\FeatureResolver;
use App\Core\Contracts\Site\SiteResolver;

class SiteFeatureResolver implements FeatureResolver
{

    /**
     * @var SiteResolver
     */
    private SiteResolver $siteResolver;

    public function __construct(SiteResolver $siteResolver)
    {
        $this->siteResolver = $siteResolver;
    }

    public function setFeature(Feature $feature): void
    {
        $site = $feature->getSite();
        $site->current_feature_id = $feature->getId();
        $site->save();

        $this->siteResolver->setSite($site);
    }

    public function getFeature(): Feature
    {
        if($this->hasFeature()) {
            return $this->siteResolver->getSite()->getCurrentFeature();
        }
        throw new \Exception('No feature is set');
    }

    public function hasFeature(): bool
    {
        return $this->siteResolver->hasSite() && $this->siteResolver->getSite()->getCurrentFeature() !== null;
    }

    public function clearFeature(): void
    {
        if($this->hasFeature()) {
            $feature = $this->getFeature();
            $site = $feature->getSite();
            $site->current_feature_id = null;
            $site->save();
        }
    }
}
