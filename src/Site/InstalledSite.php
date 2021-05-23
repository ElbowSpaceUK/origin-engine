<?php

namespace OriginEngine\Site;

use OriginEngine\Feature\Feature;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstalledSite extends Model
{
    use SoftDeletes;

    protected $table = 'sites';

    protected static function booted()
    {
        static::deleting(fn(InstalledSite $site) => $site->getFeatures()->each(fn($feature) => $feature->delete()));
    }

    public function getFeatures(): Collection
    {
        return $this->features ?? new Collection();
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

    public function getBlueprint(): string
    {
        return $this->blueprint;
    }

    public function getCurrentFeatureId(): int
    {
        return $this->current_feature_id;
    }

    public function currentFeature()
    {
        return $this->hasOne(Feature::class, 'id', 'current_feature_id');
    }

}
