<?php

namespace App\Core\Instance;

use App\Core\Helpers\Storage\Filesystem;
use App\Core\Helpers\WorkingDirectory\WorkingDirectory;

class InstanceRepository implements \App\Core\Contracts\Instance\InstanceRepository
{

    public function exists(string $instanceId): bool
    {
        return Filesystem::create()->exists(
            WorkingDirectory::fromInstanceId($instanceId)->path()
        );
    }

}
