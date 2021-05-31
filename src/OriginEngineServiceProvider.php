<?php

namespace OriginEngine;

use OriginEngine\Commands\FeatureClear;
use OriginEngine\Commands\FeatureDefault;
use OriginEngine\Commands\FeatureDelete;
use OriginEngine\Commands\FeatureList;
use OriginEngine\Commands\FeatureNew;
use OriginEngine\Commands\FeatureUse;
use OriginEngine\Commands\PostUpdate;
use OriginEngine\Commands\SiteClear;
use OriginEngine\Commands\SiteDefault;
use OriginEngine\Commands\SiteDelete;
use OriginEngine\Commands\SiteDown;
use OriginEngine\Commands\SiteList;
use OriginEngine\Commands\SiteNew;
use OriginEngine\Commands\SitePrune;
use OriginEngine\Commands\SiteReset;
use OriginEngine\Commands\SiteUp;
use OriginEngine\Commands\SiteUse;
use OriginEngine\Contracts\Feature\FeatureRepository as FeatureRepositoryContract;
use OriginEngine\Contracts\Feature\FeatureResolver;
use OriginEngine\Contracts\Helpers\Composer\OperationManager as OperationManagerContract;
use OriginEngine\Contracts\Helpers\Port\PortChecker;
use OriginEngine\Contracts\Helpers\Terminal\Executor;
use OriginEngine\Contracts\Pipeline\PipelineDownRunner as PipelineDownRunnerContract;
use OriginEngine\Contracts\Pipeline\PipelineRunner as PipelineRunnerContract;
use OriginEngine\Contracts\Site\SiteBlueprintStore as SiteBlueprintStoreContract;
use OriginEngine\Contracts\Site\SiteRepository as SiteRepositoryContract;
use OriginEngine\Contracts\Helpers\Settings\SettingRepository as SettingRepositoryContract;
use OriginEngine\Contracts\Site\SiteResolver;
use OriginEngine\Feature\FeatureRepository;
use OriginEngine\Feature\ActiveFeatureResolver;
use OriginEngine\Helpers\Composer\Operations\StandardOperationManager;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Port\FSockOpenPortChecker;
use OriginEngine\Helpers\Terminal\ShellExecutor;
use OriginEngine\Pipeline\Pipeline;
use OriginEngine\Pipeline\PipelineConfig;
use OriginEngine\Pipeline\PipelineHistory;
use OriginEngine\Pipeline\Runners\ModifyPipelineRunner;
use OriginEngine\Pipeline\Runners\PipelineDownRunner;
use OriginEngine\Pipeline\PipelineModifier;
use OriginEngine\Helpers\Settings\SettingRepository;
use OriginEngine\Pipeline\Runners\PipelineRunner;
use OriginEngine\Site\SettingsSiteResolver;
use OriginEngine\Site\SiteBlueprintStore;
use OriginEngine\Site\SiteRepository;
use OriginEngine\Plugins\Stubs\StubStore;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\ServiceProvider;

class OriginEngineServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Config $config, PipelineModifier $pipelineManager)
    {
        if (!$config->has('commands.default')) {
            $config->set('commands.default', \NunoMaduro\LaravelConsoleSummary\SummaryCommand::class);
        }

        $config->set('commands.add', array_merge([
            FeatureDelete::class,
            FeatureList::class,
            FeatureNew::class,
            FeatureUse::class,
            PostUpdate::class,
            SiteClear::class,
            SiteDelete::class,
            SiteDown::class,
            SiteList::class,
            SiteNew::class,
            SitePrune::class,
            SiteReset::class,
            SiteUp::class,
            SiteDefault::class
        ], $config->get('commands.add', [])));
        $config->set('commands.hidden', array_merge([
            \NunoMaduro\LaravelConsoleSummary\SummaryCommand::class,
            \Symfony\Component\Console\Command\HelpCommand::class,
            \Illuminate\Console\Scheduling\ScheduleRunCommand::class,
            \Illuminate\Console\Scheduling\ScheduleFinishCommand::class,
            \Illuminate\Database\Console\Migrations\MigrateCommand::class,
            \Illuminate\Database\Console\Migrations\RollbackCommand::class,
            \Illuminate\Database\Console\Migrations\StatusCommand::class,
            \Illuminate\Database\Console\Seeds\SeedCommand::class,
            \Laravel\Tinker\Console\TinkerCommand::class
        ], $config->get('commands.hidden', [])));

        $config->set('commands.remove', array_merge((\Phar::running() ? [ // Commands to remove
            \Illuminate\Database\Console\Migrations\FreshCommand::class,
            \Illuminate\Database\Console\Migrations\InstallCommand::class,
            \Illuminate\Database\Console\Migrations\RefreshCommand::class,
            \Illuminate\Database\Console\Migrations\ResetCommand::class,
            \Illuminate\Foundation\Console\VendorPublishCommand::class,
            \Illuminate\Database\Console\Migrations\MigrateMakeCommand::class,
            \Illuminate\Database\Console\WipeCommand::class,
            \Illuminate\Database\Console\Factories\FactoryMakeCommand::class,
            \Illuminate\Foundation\Console\ModelMakeCommand::class,
            \Illuminate\Database\Console\Seeds\SeederMakeCommand::class,
            \LaravelZero\Framework\Commands\MakeCommand::class,
            \LaravelZero\Framework\Commands\RenameCommand::class,
            \LaravelZero\Framework\Commands\StubPublishCommand::class,
            \LaravelZero\Framework\Commands\BuildCommand::class,
            \LaravelZero\Framework\Commands\InstallCommand::class,
        ] : []), $config->get('commands.remove', [])));

        if (!$config->has('database.default')) {
            $config->set('database.default', 'sqlite');
            $config->set('database.connections.sqlite', [
                'driver' => 'sqlite',
                'url' => env('DATABASE_URL'),
                'database' => $_SERVER['HOME'] . '/.atlas-cli/atlas-cli.sqlite',
                'prefix' => '',
                'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
            ]);
        }
        if (!$config->has('database.migrations')) {
            $config->set('database.migrations', 'migrations');
        }

        if (!$config->has('filesystems.default')) {
            $config->set('filesystems.default', 'config');
            $config->set('filesystems.connections.disks.config', [
                'driver' => 'local',
                'root' => $_SERVER['HOME'] . '/.atlas-cli'
            ]);
        }

        $this->app->extend(PipelineRunner::class, fn(PipelineRunner $pipelineRunner, $app) => new ModifyPipelineRunner($pipelineRunner));

        app(PipelineModifier::class)->extend('feature:default', function(Pipeline $pipeline) {
            $pipeline->before('set-default-feature', function(PipelineConfig $config, PipelineHistory $history) {
                IO::success('Event has been called :)');
            });
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(SiteRepositoryContract::class, SiteRepository::class);
        $this->app->bind(SettingRepositoryContract::class, SettingRepository::class);
        $this->app->bind(PortChecker::class, FSockOpenPortChecker::class);
        $this->app->bind(Executor::class, ShellExecutor::class);

        $this->app->singleton(PipelineModifier::class);
        $this->app->singleton(StubStore::class);

        $this->app->bind(OperationManagerContract::class, StandardOperationManager::class);
        $this->app->bind(FeatureRepositoryContract::class, FeatureRepository::class);

        $this->app->bind(FeatureResolver::class, ActiveFeatureResolver::class);
        $this->app->bind(SiteResolver::class, SettingsSiteResolver::class);

        $this->app->singleton(SiteBlueprintStoreContract::class, SiteBlueprintStore::class);

        $this->app->bind(PipelineRunnerContract::class, PipelineRunner::class);
        $this->app->bind(PipelineDownRunnerContract::class, PipelineDownRunner::class);

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
