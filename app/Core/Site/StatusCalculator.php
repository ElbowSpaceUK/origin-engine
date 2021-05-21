<?php

namespace App\Core\Site;

use App\Core\Helpers\Terminal\Executor;
use App\Core\Helpers\WorkingDirectory\WorkingDirectory;

class StatusCalculator
{

    public static function calculate(string $instanceId)
    {
        if(!app(\App\Core\Contracts\Instance\InstanceRepository::class)->exists($instanceId)) {
            return Site::STATUS_MISSING;
        }

        if(static::sailIsUp($instanceId)) {
            return Site::STATUS_READY;
        }

        return Site::STATUS_DOWN;
    }

    public static function sailIsUp(string $instanceId): bool
    {
        // TODO Make parallel to calculate?
        try {
            Executor::cd(
                WorkingDirectory::fromInstanceId($instanceId)
            )->execute('./vendor/bin/sail artisan help');
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
}
