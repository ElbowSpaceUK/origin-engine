<?php

namespace OriginEngine\Contracts\Feature;

use OriginEngine\Feature\Feature;
use Illuminate\Database\Eloquent\Collection;
use OriginEngine\Site\Site;

interface FeatureRepository
{

    public function allThroughSite(Site $site);

    public function all(): Collection;

    public function create(int $siteId, string $name, ?string $description, string $type, string $branch): Feature;

    public function exists(string $id): bool;

    public function count(): int;

    public function getById(int $id): Feature;

    public function delete(int $id): void;

    public function restore(int $id): void;

}
