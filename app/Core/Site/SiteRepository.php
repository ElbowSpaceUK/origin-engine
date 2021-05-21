<?php

namespace App\Core\Site;

use App\Core\Contracts\Feature\FeatureRepository;
use App\Core\Feature\Feature;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SiteRepository implements \App\Core\Contracts\Site\SiteRepository
{

    public function all(): Collection
    {
       return Site::all();
    }

    public function create(string $instanceId, string $name, string $description, string $installer): Site
    {
        $site = new Site();

        $site->instance_id = $instanceId;
        $site->name = $name;
        $site->description = $description;
        $site->installer = $installer;

        $site->save();

        return $site;
    }

    public function exists(int $id): bool
    {
        try {
            Site::findOrFail($id);
            return true;
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }

    public function count(): int
    {
        return Site::count();
    }

    public function getById(int $id): Site
    {
        return Site::findOrFail($id);
    }

    public function getByInstanceId(string $instanceId): Site
    {
        return Site::where('instance_id', $instanceId)->firstOrFail();
    }

    public function delete(int $id): void
    {
        $site = $this->getById($id);
        $site->delete();
    }

    public function instanceIdExists(string $instanceId): bool
    {
        try {
            Site::where('instance_id', $instanceId)->firstOrfail();
            return true;
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }
}
