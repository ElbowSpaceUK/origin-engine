<?php

namespace OriginEngine\Site;

use Illuminate\Database\Eloquent\Model;
use OriginEngine\Contracts\Feature\FeatureRepository;
use OriginEngine\Feature\Feature;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SiteRepository implements \OriginEngine\Contracts\Site\SiteRepository
{

    public function all(): Collection
    {
       return InstalledSite::all()->map(fn(InstalledSite $installedSite) => SiteFactory::fromInstalledSite($installedSite));
    }

    public function create(string $instanceId, string $name, string $description, string $blueprint): Site
    {
        $site = new InstalledSite();

        $site->instance_id = $instanceId;
        $site->name = $name;
        $site->description = $description;
        $site->blueprint = $blueprint;

        $site->save();

        return SiteFactory::fromInstalledSite($site);
    }

    public function exists(int $id): bool
    {
        try {
            InstalledSite::findOrFail($id);
            return true;
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }

    public function count(): int
    {
        return InstalledSite::count();
    }

    public function getById(int $id): Site
    {
        return SiteFactory::fromInstalledSite(
            InstalledSite::findOrFail($id)
        );
    }

    public function getByInstanceId(string $instanceId): Site
    {
        return SiteFactory::fromInstalledSite(
            InstalledSite::where('instance_id', $instanceId)->firstOrFail()
        );
    }

    public function delete(int $id): void
    {
        $site = InstalledSite::findOrFail($id);
        $site->delete();
    }

    public function instanceIdExists(string $instanceId): bool
    {
        return InstalledSite::where('instance_id', $instanceId)->count() > 0;
    }
}
