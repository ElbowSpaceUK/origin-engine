<?php

namespace App\Core\Site;

use App\Core\Contracts\Site\SiteResolver;
use App\Core\Feature\Feature;
use App\Core\Helpers\WorkingDirectory\WorkingDirectory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{
    use SoftDeletes;

    const STATUS_MISSING = 'missing';

    const STATUS_READY = 'ready';

    const STATUS_DOWN = 'down';

    protected $table = 'sites';

    protected static function booted()
    {
        static::deleting(fn(Site $site) => $site->getFeatures()->each(fn($feature) => $feature->delete()));
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getInstanceId(): string
    {
        return $this->instance_id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getUrl(string $envFile = '.env')
    {
        return (new UrlCalculator())->calculate($this->getInstanceId(), $envFile);
    }

    public function getStatus()
    {
        return (new StatusCalculator())->calculate($this->getInstanceId());
    }

    public function getInstaller(): string
    {
        return $this->installer;
    }

    public function getCurrentFeature(): ?Feature
    {
        return $this->currentFeature;
    }

    public function getFeatures(): Collection
    {
        return $this->features;
    }

    public function hasCurrentFeature(): bool
    {
        return $this->getCurrentFeature() !== null;
    }

    public function features()
    {
        return $this->hasMany(Feature::class);
    }

    public function currentFeature()
    {
        return $this->hasOne(Feature::class, 'id', 'current_feature_id');
    }

    public function getWorkingDirectory()
    {
        return WorkingDirectory::fromSite($this);
    }

    public static function current(): ?Site
    {
        if(app(SiteResolver::class)->hasSite()) {
            return app(SiteResolver::class)->getSite();
        }
        return null;
    }

}
