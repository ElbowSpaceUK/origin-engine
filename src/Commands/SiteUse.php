<?php

namespace OriginEngine\Commands;

use OriginEngine\Contracts\Command\Command;
use OriginEngine\Contracts\Command\SiteCommand;
use OriginEngine\Contracts\Site\SiteResolver;
use OriginEngine\Helpers\IO\IO;

class SiteUse extends SiteCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'site:use';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Use the given site by default.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(SiteResolver $siteResolver)
    {
        $site = $this->getSite('Which site would you like to use by default?', null);

        IO::info('Switching default site to ' . $site->getName() . '.');

        $siteResolver->setSite($site);

    }

}
