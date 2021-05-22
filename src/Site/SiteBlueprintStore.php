<?php

namespace OriginEngine\Site;

use OriginEngine\Contracts\Site\SiteBlueprintStore as SiteBlueprintStoreContract;

class SiteBlueprintStore implements SiteBlueprintStoreContract
{

    /**
     * @var array
     */
    private $siteBlueprints = [];

    public function register(string $alias, SiteBlueprint $blueprint)
    {
        $this->siteBlueprints[$alias] = $blueprint;
    }

    public function all(): array
    {
        return $this->siteBlueprints;
    }

    public function has(string $alias): bool
    {
        return array_key_exists($alias, $this->siteBlueprints);
    }

    public function get(string $alias): SiteBlueprint
    {
        if($this->has($alias)) {
            return $this->siteBlueprints[$alias];
        }
        throw new \Exception(sprintf(
            'Could not find the site with alias %s', $alias
        ));
    }
}
