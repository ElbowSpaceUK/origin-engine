<?php

namespace OriginEngine\Helpers\WorkingDirectory;

use OriginEngine\Helpers\Storage\Filesystem;

class ProjectDirectoryLocator
{

    public static function fromDirectory(string $directory): string
    {
        return Filesystem::project($directory);
    }

}
