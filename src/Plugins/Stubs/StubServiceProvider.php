<?php

namespace OriginEngine\Plugins\Stubs;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\ServiceProvider;
use OriginEngine\Plugins\Stubs\Commands\StubList;
use OriginEngine\Plugins\Stubs\Commands\StubMake;

class StubServiceProvider extends ServiceProvider
{

    public function boot(Config $config)
    {
        $config->set('commands.add', array_merge([
            StubList::class,
            StubMake::class
        ], $config->get('commands.add', [])));
    }

}