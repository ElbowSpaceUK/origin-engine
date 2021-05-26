<?php

namespace OriginEngine\Plugins\Dependencies;

use Illuminate\Contracts\Config\Repository as Config;
use OriginEngine\Foundation\Plugin;
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

}
