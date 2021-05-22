<?php

namespace OriginEngine\Commands;

use OriginEngine\Contracts\Command\Command;
use OriginEngine\Contracts\Site\SiteResolver;
use OriginEngine\Helpers\IO\IO;

class SiteClear extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'site:clear';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Do not use any site by default.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(SiteResolver $siteResolver)
    {
        IO::info('Clearing default site.');

        if($siteResolver->hasSite()) {
            $siteResolver->clearSite();
        }
    }

}
