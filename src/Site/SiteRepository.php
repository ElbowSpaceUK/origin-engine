<?php

namespace OriginEngine\Site;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

class SiteRepository implements \OriginEngine\Contracts\Site\SiteRepository
{

    public function all(): Collection
    {
       return InstalledSite::all()->map(fn(InstalledSite $installedSite) => new Site($installedSite));
    }

    public function create(string $instanceId, string $name, string $description, string $blueprint): Site
    {
        $site = new InstalledSite();

        $site->instance_id = $instanceId;
        $site->name = $name;
        $site->description = $description;
        $site->blueprint = $blueprint;

        $site->save();

        return new Site($site);
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
        return new Site(
            InstalledSite::findOrFail($id)
        );
    }

    public function getByInstanceId(string $instanceId): Site
    {
        return new Site(
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
