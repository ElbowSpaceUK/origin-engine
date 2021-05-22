<?php

namespace OriginEngine\Helpers\Terminal;

use Illuminate\Support\Facades\Facade;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;

/**
 * @method static string|null execute(string $command) Runs the given command and returns the output
 * @method static \OriginEngine\Contracts\Helpers\Terminal\Executor cd(WorkingDirectory $directory) Run subsequent commands in the given directory
 */
class Executor extends Facade
{

    protected static function getFacadeAccessor()
    {
        return \OriginEngine\Contracts\Helpers\Terminal\Executor::class;
    }

}
