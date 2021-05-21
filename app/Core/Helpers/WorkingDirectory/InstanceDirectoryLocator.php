<?php

namespace App\Core\Helpers\WorkingDirectory;

use App\Core\Helpers\Storage\Filesystem;

class InstanceDirectoryLocator
{

    public static function fromInstanceId(string $instanceId)
    {
        return Filesystem::project($instanceId);
    }

}
