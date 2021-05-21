<?php

namespace App\Core\Contracts\Feature;

use App\Core\Feature\Feature;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

interface FeatureRepository
{

    public function all(): Collection;

    public function create(int $siteId, string $name, string $description, string $type, string $branch): Feature;

    public function exists(string $id): bool;

    public function count(): int;

    public function getById(int $id): Feature;

    public function getByInstanceId(string $instanceId): Feature;

    public function delete(int $id): void;

}
