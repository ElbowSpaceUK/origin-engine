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

    public function create(string $directory, string $name, string $description, string $blueprint): Site
    {
        $site = new InstalledSite();

        $site->directory = $directory;
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

    public function getByDirectory(string $directory): Site
    {
        return new Site(
            InstalledSite::where('directory', $directory)->firstOrFail()
        );
    }

    public function delete(int $id): void
    {
        $site = InstalledSite::findOrFail($id);
        $site->delete();
    }

    public function directoryExists(string $directory): bool
    {
        return InstalledSite::where('directory', $directory)->count() > 0;
    }
}
