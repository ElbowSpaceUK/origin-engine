<?php

namespace App\Core\Contracts\Site;

use App\Core\Site\Site;
use Illuminate\Database\Eloquent\Collection;

interface SiteRepository
{

    public function all(): Collection;

    public function create(string $instanceId, string $name, string $description, string $installer): Site;

    public function exists(int $id): bool;

    public function instanceIdExists(string $instanceId): bool;

    public function count(): int;

    public function getById(int $id): Site;

    public function getByInstanceId(string $instanceId): Site;

    public function delete(int $id): void;
}
