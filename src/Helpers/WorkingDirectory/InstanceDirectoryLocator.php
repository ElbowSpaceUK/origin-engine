<?php

namespace OriginEngine\Helpers\WorkingDirectory;

use OriginEngine\Helpers\Storage\Filesystem;

class InstanceDirectoryLocator
{

    public static function fromInstanceId(string $instanceId)
    {
        return Filesystem::project($instanceId);
    }

}
