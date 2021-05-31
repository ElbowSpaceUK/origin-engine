<?php

namespace OriginEngine\Contracts\Feature;

use OriginEngine\Feature\Feature;
use OriginEngine\Site\Site;

interface FeatureResolver
{

    public function setFeature(Feature $feature): void;

    public function getFeature(Site $site): Feature;

    public function hasFeature(Site $site): bool;

    public function clearFeature(Site $site): void;

}
