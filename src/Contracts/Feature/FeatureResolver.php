<?php

namespace OriginEngine\Contracts\Feature;

use OriginEngine\Feature\Feature;

interface FeatureResolver
{

    public function setFeature(Feature $feature): void;

    public function getFeature(): Feature;

    public function hasFeature(): bool;

    public function clearFeature(): void;

}
