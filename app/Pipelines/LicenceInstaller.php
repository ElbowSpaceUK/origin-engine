<?php

namespace App\Pipelines;

use App\Core\Pipeline\Pipeline;
use App\Core\Pipeline\Tasks\InstallYarnDependencies;
use App\Core\Pipeline\Tasks\RunYarnScript;
use App\Core\Pipeline\Tasks\WaitForDocker;

class LicenceInstaller extends Pipeline
{

    protected function getTasks(): array
    {
        return [
            \App\Core\Pipeline\Tasks\CloneGitRepository::provision('git@github.com:ElbowSpaceUK/licensing', 'develop')
                ->withName('Downloading'),

            \App\Core\Pipeline\Tasks\InstallComposerDependencies::provision()->withName('Installing composer dependencies'),

            \App\Core\Pipeline\Tasks\CopyEnvironmentFile::provision('.env.example', '.env')
                ->withName('Set up local environment file'),
            
            \App\Core\Pipeline\Tasks\ValidatePortEntries::provision(
                '.env',
                ['APP_PORT', 'FORWARD_DB_PORT', 'FORWARD_MAILHOG_PORT', 'FORWARD_MAILHOG_DASHBOARD_PORT', 'FORWARD_REDIS_PORT', 'FORWARD_MEILISEARCH_PORT', 'FORWARD_SELENIUM_PORT'],
                ['HTTP', 'database', 'mail', 'mail dashboard', 'redis', 'meilisearch', 'selenium'],
                false)
                ->withName('Verifying port assignments'),

            \App\Core\Pipeline\Tasks\BringEnvironmentUp::provision(true),

            WaitForDocker::provision()
                ->withName('Waiting for Docker'),

            \App\Core\Pipeline\Tasks\GenerateApplicationKey::provision('local')
                ->withName('Create local application key'),

            \App\Core\Pipeline\Tasks\MigrateDatabase::provision('local')
                ->withName('Migrate the local database'),
        ];
    }
}
