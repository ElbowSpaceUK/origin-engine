<?php

namespace OriginEngine\Plugins\Stubs;

use OriginEngine\Foundation\Plugin;
use OriginEngine\Plugins\Stubs\Commands\StubList;
use OriginEngine\Plugins\Stubs\Commands\StubMake;

class StubPlugin extends Plugin
{

    protected array $commands = [
        StubList::class,
        StubMake::class
    ];

}
