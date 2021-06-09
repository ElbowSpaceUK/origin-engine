<?php

namespace OriginEngine\Feature;

use OriginEngine\Contracts\Feature\FeatureResolver;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Plugins\Dependencies\LocalPackage;
use OriginEngine\Site\InstalledSite;
use OriginEngine\Site\Site;
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
        'name', 'description', 'type', 'site_id', 'branch', 'is_dependency'
    ];

    protected $casts = [
        'is_dependency' => 'boolean'
    ];

    public function site()
    {
        return $this->belongsTo(InstalledSite::class);
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
        return new Site($this->site);
    }

    public function getBranch()
    {
        return $this->branch;
    }

    public function isDependency(): bool
    {
        return $this->is_dependency;
    }

    public static function getDefaultBranchName(string $type, string $name): string
    {
        $branchPrefix = 'feature';
        if($type === 'fixed') {
            $branchPrefix = 'bug';
        }
        return sprintf('%s/%s', $branchPrefix, Str::kebab($name));
    }

    public function getDirectory(): Directory
    {
        $path = $this->getSite()->getDirectory()->path();

        if($this->isDependency()) {
            $path = Filesystem::append(
                $path,
                sprintf('repos/%s', LocalPackage::where('feature_id', $this->getId())->firstOrFail()->getName())
            );
        }

        return Directory::fromFullPath($path);
    }
}
