<?php

namespace OriginEngine\Plugins\Dependencies;

use OriginEngine\Feature\Feature;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class LocalPackage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'url', 'type', 'original_version', 'feature_id', 'parent_feature_id', 'is_local'
    ];

    protected $casts = [
        'is_local' => 'boolean'
    ];

    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }

    public function isLocal(): bool
    {
        return $this->is_local;
    }

    public function parentFeature()
    {
        return $this->belongsTo(Feature::class, 'parent_feature_id');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getOriginalVersion(): ?string
    {
        return $this->original_version;
    }

    public function getFeatureId(): int
    {
        return $this->feature_id;
    }

    public function getFeature(): Feature
    {
        return $this->feature;
    }

    public function getParentFeature(): Feature
    {
        return $this->parentFeature;
    }

    public function getPathRelativeToRoot(): string
    {
        return sprintf('repos/%s', $this->getName());
    }
}
