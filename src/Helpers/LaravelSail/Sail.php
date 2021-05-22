<?php

namespace OriginEngine\Site;

use OriginEngine\Helpers\Terminal\Executor;

class Sail
{

    public static function isRunning(Site $site): bool
    {
        // TODO Make parallel to calculate?
        try {
            Executor::cd(
                $site->getWorkingDirectory()
            )->execute('./vendor/bin/sail artisan help');
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
}
