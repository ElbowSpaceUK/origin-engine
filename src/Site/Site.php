<?php

namespace OriginEngine\Site;

use OriginEngine\Contracts\Site\SiteResolver;
use OriginEngine\Feature\Feature;
use OriginEngine\Helpers\Env\EnvRepository;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site
{

    const STATUS_MISSING = 'missing';

    const STATUS_READY = 'ready';

    const STATUS_DOWN = 'down';

    private InstalledSite $installedSite;

    public function __construct(InstalledSite $installedSite)
    {
        $this->installedSite = $installedSite;
    }

    protected function getModel(): InstalledSite
    {
        return $this->installedSite;
    }

    public function getBlueprint(): SiteBlueprint
    {
        return app(\OriginEngine\Contracts\Site\SiteBlueprintStore::class)->get(
            $this->getModel()->getBlueprint()
        );
    }

    public static function current(): ?Site
    {
        if (app(SiteResolver::class)->hasSite()) {
            return app(SiteResolver::class)->getSite();
        }
        return null;
    }

    public function getFeatures(): Collection
    {
        return $this->getModel()->getFeatures();
    }

    public function getId(): int
    {
        return $this->getModel()->getId();
    }

    public function getInstanceId(): string
    {
        return $this->getModel()->getInstanceId();
    }

    public function getName(): string
    {
        return $this->getModel()->getName();
    }

    public function getDescription(): string
    {
        return $this->getModel()->getDescription();
    }

    public function getUrl()
    {
        return $this->getBlueprint()->getUrl($this);
    }

    public function getWorkingDirectory()
    {
        return WorkingDirectory::fromSite($this);
    }

    public function getStatus()
    {
        return $this->getBlueprint()->getStatus($this);
    }

    public function getBlueprintAlias(): string
    {
        return $this->getModel()->getBlueprint();
    }

    public function hasCurrentFeature(): bool
    {
        return $this->getCurrentFeature() !== null;
    }

    public function getCurrentFeature(): ?Feature
    {
        return $this->currentFeature;
    }

    public function features()
    {
        return $this->hasMany(Feature::class);
    }

    public function currentFeature()
    {
        return $this->hasOne(Feature::class, 'id', 'current_feature_id');
    }

}
