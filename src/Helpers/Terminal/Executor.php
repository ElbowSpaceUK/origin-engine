<?php

namespace OriginEngine\Helpers\Terminal;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string|null execute(string $command) Runs the given command and returns the output
 * @method static \OriginEngine\Contracts\Helpers\Terminal\Executor cd(string $directory) Run subsequent commands in the given directory
 */
class Executor extends Facade
{

    protected static function getFacadeAccessor()
    {
        return \OriginEngine\Contracts\Helpers\Terminal\Executor::class;
    }

}
