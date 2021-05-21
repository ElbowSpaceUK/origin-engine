<?php

namespace App\Commands;

use App\Core\Contracts\Site\SiteRepository;
use App\Core\Site\Site;
use App\Core\Contracts\Command\Command;

class SiteList extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'site:list';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List all sites currently installed.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(SiteRepository $siteRepository)
    {
        $sites = $siteRepository->all();
        $currentSite = Site::current();

        $this->table(
            ['', 'ID', 'Name', 'Description', 'Status', 'URL'],
            $sites->map(function(Site $site) use ($currentSite){
                return [
                    ($currentSite !== null && $currentSite->is($site) ? '*' : ''),
                    $site->getId(),
                    $site->getName(),
                    $site->getDescription(),
                    $site->getStatus(),
                    $site->getUrl()
                ];
            })
        );
    }
}
