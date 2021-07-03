<?php

namespace OriginEngine\Update;

use Humbug\SelfUpdate\Strategy\GithubStrategy;
use Humbug\SelfUpdate\Updater;
use Humbug\SelfUpdate\VersionParser;
use LaravelZero\Framework\Components\Updater\Strategy\StrategyInterface;

class GithubPrivateReleaseStrategy extends GithubStrategy implements StrategyInterface
{

    public function download(Updater $updater)
    {
        dump('download');
        dd($updater);

        parent::download($updater);
    }

    /**
     * Retrieve the current version of the local phar file.
     *
     * @param Updater $updater
     * @return string
     */
    public function getCurrentLocalVersion(Updater $updater)
    {
        dump('local-version');
        dd($updater);

        return parent::getCurrentLocalVersion($updater);
    }

    public function getCurrentRemoteVersion(Updater $updater)
    {
        dump('remote-version');
        dd($updater);

        return parent::getCurrentRemoteVersion($updater);

    }
}
