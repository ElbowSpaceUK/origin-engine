<?php

namespace App;

use App\Core\Contracts\Feature\FeatureRepository as FeatureRepositoryContract;
use App\Core\Contracts\Feature\FeatureResolver;
use App\Core\Contracts\Helpers\Composer\OperationManager as OperationManagerContract;
use App\Core\Contracts\Helpers\Port\PortChecker;
use App\Core\Contracts\Helpers\Terminal\Executor;
use App\Core\Contracts\Instance\InstanceRepository as InstanceManagerContract;
use App\Core\Contracts\Site\SiteRepository as SiteRepositoryContract;
use App\Core\Contracts\Helpers\Settings\SettingRepository as SettingRepositoryContract;
use App\Core\Contracts\Site\SiteResolver;
use App\Core\Feature\FeatureRepository;
use App\Core\Feature\SiteFeatureResolver;
use App\Core\Helpers\Composer\Operations\StandardOperationManager;
use App\Core\Helpers\IO\IO;
use App\Core\Helpers\Port\FSockOpenPortChecker;
use App\Core\Helpers\Terminal\ShellExecutor;
use App\Core\Pipeline\PipelineManager;
use App\Core\Instance\InstanceRepository;
use App\Core\Helpers\Settings\SettingRepository;
use App\Core\Site\SettingsSiteResolver;
use App\Core\Site\SiteRepository;
use App\Core\Stubs\Stubs;
use App\Core\Stubs\StubStore;
use App\Pipelines\CMSInstaller;
use App\Pipelines\FrontendInstaller;
use App\Pipelines\LicenceInstaller;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Repository $config, Stubs $stubs)
    {
        app(PipelineManager::class)->extend('cms', function(Container $container) {
            return $container->make(CMSInstaller::class);
        });
        app(PipelineManager::class)->extend('frontend', function(Container $container) {
            return $container->make(FrontendInstaller::class);
        });
        app(PipelineManager::class)->extend('licensing', function(Container $container) {
            return $container->make(LicenceInstaller::class);
        });

        $stubs->newStub('routes', 'A routes file for a demo', 'routes')
            ->addFile(
                $stubs->newStubFile(
                    __DIR__ . '/../stubs/test/routes.api.php.stub', 'api.php'
                )
                    ->addReplacement(
                        $stubs->newSectionReplacement('extraRoute', 'Would you like an extra route?', true, null, [
                            $stubs->newStringReplacement('extraRouteText', 'What should the route return', 'Testing')
                        ])
                    )
            )->addFile(
                $stubs->newStubFile(
                    __DIR__ . '/../stubs/test/routes.web.php.stub', 'web.php'
                )
                    ->addReplacement($stubs->newStringReplacement('path', 'What is the route?', 'default-route'))
                    ->addReplacement(
                        $stubs->newArrayReplacement('models', 'What is the name of the models?', [], null,
                            $stubs->newStringReplacement('model', 'What is the model name?', 'Model'))
                    )
            )->addFile(
                $stubs->newStubFile(
                    __DIR__ . '/../stubs/test/web.php.backup.stub', fn($data) => sprintf('%s.php', $data['routesFileName']), 'secondary',
                    fn($data) => IO::confirm('Would you like to publish the optional routes file?')
                )
                    ->addReplacement($stubs->newStringReplacement('routesFileName', 'Name of the routes file?', 'route-file-name'))
                    ->addReplacement($stubs->newBooleanReplacement('includePost', 'Should we include a post request?', false))
                    ->addReplacement($stubs->newArrayReplacement('dbColumns', 'Define the cols', [], null,
                        $stubs->newTableColumnReplacement('dbColumns', 'What columns do you want for your xyz?', [])))
            );

        // Aim for the API

//        $stubs->newStub('routes', 'A routes file for a demo', 'routes')
//            ->addFile(__DIR__ . '/../stubs/test/routes.api.php.stub', 'api.php')
//            ->addFile(__DIR__ . '/../stubs/test/routes.web.php.stub', 'web.php')
//            ->addFile(__DIR__ . '/../stubs/test/web.php.backup.stub', fn($data) => sprintf('%s.php', $data['routesFileName']), 'secondary',
//                fn($data) => IO::confirm('Would you like to publish the optional routes file?')
//            )
//            ->addReplacement(
//                $stubs->newSectionReplacement('extraRoute', 'Would you like an extra route?', true, null, [
//                    $stubs->newStringReplacement('extraRouteText', 'What should the route return', 'Testing')
//                ])
//            )
//            ->addReplacement($stubs->newStringReplacement('path', 'What is the route?', 'default-route'))
//            ->addReplacement(
//                $stubs->newArrayReplacement('models', 'What is the name of the models?', [], null,
//                    $stubs->newStringReplacement('model', 'What is the model name?', 'Model'))
//            )
//            ->addReplacement($stubs->newStringReplacement('routesFileName', 'Name of the routes file?', 'route-file-name'))
//            ->addReplacement($stubs->newBooleanReplacement('includePost', 'Should we include a post request?', false))
//            ->addReplacement($stubs->newArrayReplacement('dbColumns', 'Define the cols', [], null,
//                $stubs->newTableColumnReplacement('dbColumns', 'What columns do you want for your xyz?', []))
//            );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(ValidationServiceProvider::class);
        $this->app->bind(SiteRepositoryContract::class, SiteRepository::class);
        $this->app->bind(InstanceManagerContract::class, InstanceRepository::class);
        $this->app->bind(SettingRepositoryContract::class, SettingRepository::class);
        $this->app->bind(PortChecker::class, FSockOpenPortChecker::class);
        $this->app->bind(Executor::class, ShellExecutor::class);

        $this->app->singleton(PipelineManager::class);
        $this->app->singleton(StubStore::class);

        $this->app->bind(OperationManagerContract::class, StandardOperationManager::class);
        $this->app->bind(FeatureRepositoryContract::class, FeatureRepository::class);

        $this->app->bind(FeatureResolver::class, SiteFeatureResolver::class);
        $this->app->bind(SiteResolver::class, SettingsSiteResolver::class);
    }
}
