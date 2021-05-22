<?php

namespace OriginEngine\Helpers\Port;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool isTaken(int $port) Checks if the port is taken
 * @method static bool isFree(int $port) Checks if the port is free
 */
class Port extends Facade
{

    public static function getFacadeAccessor()
    {
        return \OriginEngine\Contracts\Helpers\Port\PortChecker::class;
    }

}
