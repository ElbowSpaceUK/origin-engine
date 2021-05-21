<?php

namespace App\Commands;

use App\Core\Contracts\Site\SiteRepository;
use App\Core\Helpers\IO\IO;
use App\Core\Contracts\Command\Command;
use App\Core\Site\Site;

class SitePrune extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'site:prune';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Remove all sites that are missing in the project directory.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(SiteRepository $siteRepository)
    {
        $sites = $siteRepository->all();
        if(count($sites) > 0) {
            foreach($sites as $site) {
                if($site->getStatus() === Site::STATUS_MISSING) {
                    $siteRepository->delete($site->getInstanceId());
                    IO::info(sprintf('Cleared site %s', $site->name));
                }
            }
        } else {
            IO::info('No sites need pruning.');
        }
    }

}
