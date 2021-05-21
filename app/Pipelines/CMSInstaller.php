<?php

namespace App\Pipelines;

use App\Core\Pipeline\Pipeline;
use App\Core\Pipeline\Tasks\InstallYarnDependencies;
use App\Core\Pipeline\Tasks\RunYarnScript;
use App\Core\Pipeline\Tasks\WaitForDocker;

class CMSInstaller extends Pipeline
{

    protected function getTasks(): array
    {
        return [
            \App\Core\Pipeline\Tasks\CloneGitRepository::provision('git@github.com:ElbowSpaceUK/AtlasCMS-Laravel-Template', 'remove-module-installer')
                ->withName('Downloading the CMS'),

            \App\Core\Pipeline\Tasks\InstallComposerDependencies::provision()->withName('Installing composer dependencies'),

            \App\Core\Pipeline\Tasks\CopyEnvironmentFile::provision('.env.sail.example', '.env.local')
                ->withName('Set up local environment file'),
            \App\Core\Pipeline\Tasks\ValidatePortEntries::provision(
                '.env.local',
                ['APP_PORT', 'FORWARD_DB_PORT', 'FORWARD_MAILHOG_PORT', 'FORWARD_MAILHOG_DASHBOARD_PORT', 'FORWARD_REDIS_PORT', 'FORWARD_SELENIUM_PORT', 'FORWARD_DB_TESTING_PORT'],
                ['HTTP', 'database', 'mail', 'mail dashboard', 'redis', 'selenium', 'test database'],
                false)
                ->withName('Verifying port assignments'),

            \App\Core\Pipeline\Tasks\CopyEnvironmentFile::provision('.env.local', '.env.testing', [
                'APP_ENV' => 'testing', 'DB_CONNECTION' => 'mysql_testing'
            ])->withName('Set up testing environment file'),

            \App\Core\Pipeline\Tasks\CopyEnvironmentFile::provision('.env.local', '.env.dusk.local', [
                'APP_ENV' => 'testing', 'DB_CONNECTION' => 'mysql_testing'
            ])->withName('Set up dusk environment file'),

            \App\Core\Pipeline\Tasks\BringEnvironmentUp::provision(true),

            WaitForDocker::provision()
                ->withName('Waiting for Docker'),

            InstallYarnDependencies::provision('/var/www/html/vendor/elbowspaceuk/core-module'),

//            RunYarnScript::provision('dev', '/var/www/html/vendor/elbowspaceuk/core-module')
//                ->withName('Compile frontend assets'),

            \App\Core\Pipeline\Tasks\GenerateApplicationKey::provision('local')
                ->withName('Create local application key'),

            \App\Core\Pipeline\Tasks\GenerateApplicationKey::provision('testing')
                ->withName('Create testing application key'),

            \App\Core\Pipeline\Tasks\GenerateApplicationKey::provision('dusk.local')
                ->withName('Create dusk application key'),

            \App\Core\Pipeline\Tasks\MigrateDatabase::provision('local')
                ->withName('Migrate the local database'),

            \App\Core\Pipeline\Tasks\MigrateDatabase::provision('testing')
                ->withName('Migrate the testing database'),

            \App\Core\Pipeline\Tasks\SeedLaravelModule::provision('Core', 'CoreDatabaseSeeder', 'local')
                ->withName('Seed the local database')
        ];
    }
}
