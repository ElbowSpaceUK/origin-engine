<?php

namespace OriginEngine\Plugins\Dependencies;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\Collection;
use OriginEngine\Commands\Pipelines\CheckoutFeature;
use OriginEngine\Foundation\Plugin;
use OriginEngine\Helpers\Artisan\Artisan;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Pipeline;
use OriginEngine\Pipeline\PipelineModifier;
use OriginEngine\Pipeline\Tasks\Utils\Closure;
use OriginEngine\Plugins\Dependencies\Commands\DepList;
use OriginEngine\Plugins\Dependencies\Commands\DepLocal;
use OriginEngine\Plugins\Dependencies\Commands\DepMake;
use OriginEngine\Plugins\Dependencies\Commands\DepRemote;
use OriginEngine\Plugins\Dependencies\Contracts\LocalPackageRepository as LocalPackageRepositoryContract;

class DependencyPlugin extends Plugin
{

    protected array $commands = [
        DepList::class,
        DepLocal::class,
        DepMake::class,
        DepRemote::class,
    ];

    public function register()
    {
        $this->app->bind(LocalPackageRepositoryContract::class, LocalPackageDatabaseRepository::class);
        parent::register();
    }

    public function boot(Config $config)
    {
        parent::boot($config);

        $pipelineModifier = app(PipelineModifier::class);
        $pipelineModifier->extend('feature:use', function(Pipeline $pipeline) {
            if(isset($pipeline->feature)) {
                $repo = app(LocalPackageRepositoryContract::class);
                foreach($repo->getAllThroughFeature($pipeline->feature->getId()) as $localPackage) {
                    $pipeline->runTaskBefore('reset-site', 'set-local-dependencies-' . $localPackage->getName(), new Closure(
                        fn(Collection $config, Directory $directory) => (new LocalPackageHelper())->makeLocal($localPackage, $directory),
                        fn(Collection $config, Directory $directory) => (new LocalPackageHelper())->makeRemote($localPackage, $directory),
                    ));
                }
            }
        });

        $pipelineModifier->extend('site:reset', function(Pipeline $pipeline) {
            if(isset($pipeline->site) && $pipeline->site->hasCurrentFeature()) {
                $repo = app(LocalPackageRepositoryContract::class);
                foreach($repo->getAllThroughFeature($pipeline->site->getCurrentFeature()->getId()) as $localPackage) {
                    $pipeline->runTaskBefore('checkout-branch', 'remove-local-dependencies-' . $localPackage->getName(), new Closure(
                        fn(Collection $config, Directory $directory) => (new LocalPackageHelper())->makeRemote($localPackage, $directory),
                        fn(Collection $config, Directory $directory) => (new LocalPackageHelper())->makeLocal($localPackage, $directory)
                    ));
                }
            }
        });
    }

}
