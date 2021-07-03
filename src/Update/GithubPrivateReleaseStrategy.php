<?php

namespace OriginEngine\Update;

use Humbug\SelfUpdate\Updater;
use LaravelZero\Framework\Components\Updater\Strategy\StrategyInterface;

class GithubPrivateReleaseStrategy implements StrategyInterface
{

    public function download(Updater $updater)
    {
        // TODO: Implement download() method.
    }

    public function getCurrentRemoteVersion(Updater $updater)
    {
        // TODO: Implement getCurrentRemoteVersion() method.
    }

    public function getCurrentLocalVersion(Updater $updater)
    {
        // TODO: Implement getCurrentLocalVersion() method.
    }
}
