<?php

namespace OriginEngine\Instance;

use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;

class DirectoryValidator implements \OriginEngine\Contracts\Instance\DirectoryValidator
{

    public function isValid(string $directory): bool
    {
        return Filesystem::create()->exists(
            WorkingDirectory::fromDirectory($directory)->path()
        );
    }

}
