<?php

namespace OriginEngine\Commands;

use OriginEngine\Commands\Pipelines\PruneDatabase;
use OriginEngine\Contracts\Site\SiteRepository;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Contracts\Command\Command;
use OriginEngine\Pipeline\RunsPipelines;

class SitePrune extends Command
{
    use RunsPipelines;

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
    protected $description = 'Prune your installation to tidy up any corrupted data.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(SiteRepository $siteRepository)
    {
        $sites = $siteRepository->all()->toArray();

        $this->runPipeline(new PruneDatabase($sites), Directory::fromFullPath(sys_get_temp_dir()));
    }

}
