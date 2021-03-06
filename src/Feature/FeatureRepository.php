<?php

namespace OriginEngine\Feature;

use OriginEngine\Contracts\Feature\FeatureRepository as FeatureRepositoryContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OriginEngine\Site\Site;

class FeatureRepository implements FeatureRepositoryContract
{

    public function all(): Collection
    {
        return Feature::all();
    }

    public function create(int $siteId, string $name, ?string $description, string $type, string $branch, bool $isDependency = false): Feature
    {
        $feature = new Feature();

        $feature->site_id = $siteId;
        $feature->name = $name;
        $feature->description = $description;
        $feature->type = $type;
        $feature->branch = $branch;
        $feature->is_dependency = $isDependency;

        $feature->save();

        return $feature;
    }

    public function exists(string $id): bool
    {
        try {
            Feature::findOrFail($id);
            return true;
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }

    public function count(): int
    {
        return Feature::count();
    }

    public function getById(int $id): Feature
    {
        return Feature::findOrFail($id);
    }

    public function delete(int $id): void
    {
        $feature = $this->getById($id);
        $feature->delete();
    }

    public function restore(int $id): void
    {
        $feature = Feature::onlyTrashed()->findOrFail($id);
        $feature->restore();
    }

    public function allThroughSite(Site $site): Collection
    {
        return Feature::where('site_id', $site->getId())->get();
    }

    public function getByBranchAndSite(Site $site, string $branch): Feature
    {
        return Feature::where([
            'branch' => $branch,
            'site_id' => $site->getId()
        ])->firstOrFail();
    }

}
