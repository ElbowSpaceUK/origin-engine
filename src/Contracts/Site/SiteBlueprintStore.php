<?php

namespace OriginEngine\Contracts\Site;

use OriginEngine\Site\SiteBlueprint;

interface SiteBlueprintStore
{
    public function register(string $alias, SiteBlueprint $blueprint);

    public function all(): array;

    public function has(string $alias): bool;

    public function get(string $alias): SiteBlueprint;

}
