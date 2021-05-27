<?php

namespace OriginEngine\Commands;

use OriginEngine\Commands\Pipelines\SiteReset as SiteResetPipeline;
use OriginEngine\Contracts\Command\Command;
use OriginEngine\Contracts\Command\SiteCommand;
use OriginEngine\Contracts\Feature\FeatureResolver;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\RunsPipelines;
use OriginEngine\Plugins\Dependencies\LocalPackage;
use OriginEngine\Plugins\Dependencies\LocalPackageHelper;
use Cz\Git\GitException;
use Cz\Git\GitRepository;
use OriginEngine\Plugins\Dependencies\Contracts\LocalPackageRepository;

class SiteReset extends SiteCommand
{
    use RunsPipelines;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'site:reset
                            {--B|branch= : The name of the branch to check out}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Reset a site back to its starting point, as if it were a fresh install.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(FeatureResolver $featureResolver, LocalPackageHelper $localPackageHelper)
    {
        $site = $this->getSite('Which site would you like to reset?');
        $this->runPipeline(new SiteResetPipeline(), $site->getDirectory());
    }

}
