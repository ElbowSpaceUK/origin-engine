<?php

namespace OriginEngine\Site;

use OriginEngine\Contracts\Feature\FeatureRepository;
use OriginEngine\Contracts\Feature\FeatureResolver;
use OriginEngine\Contracts\Site\SiteResolver;
use OriginEngine\Feature\Feature;
use OriginEngine\Helpers\Env\EnvRepository;
use OriginEngine\Helpers\Directory\Directory;
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

    public function getModel(): InstalledSite
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

    public function getName(): string
    {
        return $this->getModel()->getName();
    }

    public function getDescription(): string
    {
        return $this->getModel()->getDescription();
    }

    public function setDirectory(Directory $directory): void
    {
        $model = $this->getModel();
        $model->directory = $directory;
        $model->save();
    }

    public function setName(string $name): void
    {
        $model = $this->getModel();
        $model->name = $name;
        $model->save();
    }

    public function setDescription(string $description): void
    {
        $model = $this->getModel();
        $model->description = $description;
        $model->save();
    }

    public function getUrls()
    {
        return $this->getBlueprint()->getUrls($this);
    }

    public function getDirectory(): Directory
    {
        return Directory::fromDirectory($this->getDirectoryPath());
    }

    public function getDirectoryPath(): string
    {
        return $this->getModel()->getDirectoryPath();
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
        return app(FeatureResolver::class)->hasFeature($this);
    }

    public function getCurrentFeature(): ?Feature
    {
        return app(FeatureResolver::class)->getFeature($this);
    }

    public function features()
    {
        return app(FeatureRepository::class)->getFeature($this);
    }

}
