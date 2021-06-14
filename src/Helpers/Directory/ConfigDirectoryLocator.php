<?php

namespace OriginEngine\Helpers\Directory;

use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Helpers\Terminal\Executor;

class ConfigDirectoryLocator
{

    public static function locate(): string
    {
        $home = Executor::cd(Directory::fromFullPath('~'))->execute('pwd');

        return Filesystem::append($home, '.origin');
    }

}
