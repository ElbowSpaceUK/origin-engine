<?php

namespace OriginEngine\Instance;

use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;

class InstanceRepository implements \OriginEngine\Contracts\Instance\InstanceRepository
{

    public function exists(string $instanceId): bool
    {
        return Filesystem::create()->exists(
            WorkingDirectory::fromInstanceId($instanceId)->path()
        );
    }

}
