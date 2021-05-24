<?php

namespace OriginEngine\Helpers\WorkingDirectory;

use OriginEngine\Contracts\Helpers\Directory\DirectoryValidator as DirectoryValidatorContract;
use OriginEngine\Helpers\Storage\Filesystem;

class DirectoryValidator implements DirectoryValidatorContract
{

    public function isValid(string $directory): bool
    {
        return Filesystem::create()->exists(
            WorkingDirectory::fromDirectory($directory)->path()
        );
    }

}
