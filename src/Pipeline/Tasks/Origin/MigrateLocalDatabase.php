<?php

namespace OriginEngine\Pipeline\Tasks\Origin;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Task;
use OriginEngine\Pipeline\TaskResponse;

class MigrateLocalDatabase extends Task
{

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        Artisan::call('migrate', ['--force' => true]);

        return $this->succeeded();
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        Artisan::call('migrate:rollback', ['--force' => true]);
    }

    protected function upName(Collection $config): string
    {
        return 'Migrating the local database';
    }

    protected function downName(Collection $config): string
    {
        return 'Rolling back the local database';
    }
}
