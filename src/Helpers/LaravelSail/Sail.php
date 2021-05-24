<?php

namespace OriginEngine\Helpers\LaravelSail;

use OriginEngine\Helpers\Terminal\Executor;
use OriginEngine\Site\Site;

class Sail
{

    public static function isRunning(Site $site): bool
    {
        // TODO Make parallel to calculate?
        try {
            Executor::cd(
                $site->getDirectory()
            )->execute('./vendor/bin/sail artisan help');
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
}
