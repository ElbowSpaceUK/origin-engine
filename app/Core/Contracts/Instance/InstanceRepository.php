<?php

namespace App\Core\Contracts\Instance;

/**
 * Interface InstanceRepository
 * @package App\Core\Contracts\Instance
 *
 * @todo Convert into an instance checker, exists is the only thing it should ever do
 */
interface InstanceRepository
{

    public function exists(string $instanceId): bool;

}
