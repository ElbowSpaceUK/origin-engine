<?php

namespace OriginEngine\Plugins\Dependencies;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\ServiceProvider;
use OriginEngine\Plugins\Dependencies\Commands\DepList;
use OriginEngine\Plugins\Dependencies\Commands\DepLocal;
use OriginEngine\Plugins\Dependencies\Commands\DepMake;
use OriginEngine\Plugins\Dependencies\Commands\DepRemote;
use OriginEngine\Plugins\Dependencies\Contracts\LocalPackageRepository as LocalPackageRepositoryContract;

class DependencyServiceProvider extends ServiceProvider
{

    public function boot(Config $config)
    {
        $config->set('commands.add', array_merge([
            DepList::class,
            DepLocal::class,
            DepMake::class,
            DepRemote::class,
        ], $config->get('commands.add', [])));
    }

    public function register()
    {
        $this->app->bind(LocalPackageRepositoryContract::class, LocalPackageDatabaseRepository::class);
    }

}