<?php

namespace App\Commands;

use App\Core\Contracts\Command\Command;
use App\Core\Contracts\Site\SiteResolver;
use App\Core\Helpers\IO\IO;

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
