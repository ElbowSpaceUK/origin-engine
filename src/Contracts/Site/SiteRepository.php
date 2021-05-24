<?php

namespace OriginEngine\Contracts\Site;

use OriginEngine\Site\Site;
use Illuminate\Support\Collection;

interface SiteRepository
{

    public function all(): Collection;

    public function create(string $directory, string $name, string $description, string $blueprint): Site;

    public function exists(int $id): bool;

    public function directoryExists(string $directory): bool;

    public function count(): int;

    public function getById(int $id): Site;

    public function getByDirectory(string $directory): Site;

    public function delete(int $id): void;
}
