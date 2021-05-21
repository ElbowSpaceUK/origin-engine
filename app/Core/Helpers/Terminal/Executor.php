<?php

namespace App\Core\Helpers\Terminal;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string|null execute(string $command) Runs the given command and returns the output
 * @method static \App\Core\Contracts\Helpers\Terminal\Executor cd(string $directory) Run subsequent commands in the given directory
 */
class Executor extends Facade
{

    protected static function getFacadeAccessor()
    {
        return \App\Core\Contracts\Helpers\Terminal\Executor::class;
    }

}
