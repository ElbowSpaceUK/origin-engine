<?php

namespace App\Core\Packages;

use App\Core\Feature\Feature;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class LocalPackage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'url', 'type', 'original_version', 'feature_id', 'branch'
    ];

    public function feature()
    {
        return $this->belongsTo(Feature::class);
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

    public function getBranch(): string
    {
        return $this->branch;
    }

    public function getFeatureId(): int
    {
        return $this->feature_id;
    }
}
