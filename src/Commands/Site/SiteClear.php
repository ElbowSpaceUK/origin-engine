<?php

namespace OriginEngine\Commands\Site;

use OriginEngine\Commands\Pipelines\ClearDefaultSite;
use OriginEngine\Command\Command;
use OriginEngine\Contracts\Site\SiteResolver;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Pipeline\RunsPipelines;

class SiteClear extends Command
{
    use RunsPipelines;

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
    public function handle()
    {
        $this->runPipeline(new ClearDefaultSite(), Directory::fromFullPath(sys_get_temp_dir()));
    }

}
