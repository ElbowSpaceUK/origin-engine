<?php

namespace App\Core\Feature;

use App\Core\Contracts\Feature\FeatureResolver;
use App\Core\Contracts\Site\SiteResolver;
use App\Core\Packages\LocalPackage;
use App\Core\Site\Site;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Feature extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'features';

    protected $fillable = [
        'name', 'description', 'type', 'site_id', 'branch'
    ];

    protected static function booted()
    {
        static::deleting(fn(Feature $feature) => $feature->getLocalPackages()->each(function($package){
            $package->delete();
        }));
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getSite(): Site
    {
        return $this->site;
    }

    public function getLocalPackages(): Collection
    {
        return $this->localPackages;
    }

    public function localPackages()
    {
        return $this->hasMany(LocalPackage::class);
    }

    public function getBranch()
    {
        return $this->branch;
    }

    public static function getDefaultBranchName(string $type, string $name): string
    {
        $branchPrefix = 'feature';
        if($type === 'fixed') {
            $branchPrefix = 'bug';
        }
        return sprintf('%s/%s', $branchPrefix, Str::kebab($name));
    }

    public static function current(): ?Feature
    {
        if(app(FeatureResolver::class)->hasFeature()) {
            return app(FeatureResolver::class)->getFeature();
        }
        return null;
    }
}
