<?php

namespace OriginEngine\Commands;

use OriginEngine\Contracts\Site\SiteRepository;
use OriginEngine\Site\Site;
use OriginEngine\Contracts\Command\Command;

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
                    (($currentSite !== null && $currentSite->getId() === $site->getId()) ? '*' : ''),
                    $site->getId(),
                    $site->getName(),
                    $site->getDescription(),
                    $site->getStatus(),
                    collect($site->getUrls())->map(fn($url, $name) => sprintf('%s: %s', $name, $url))->join(PHP_EOL)
                ];
            })
        );
    }
}
