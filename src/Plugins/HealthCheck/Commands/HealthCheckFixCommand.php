<?php

namespace OriginEngine\Plugins\HealthCheck\Commands;

use Illuminate\Container\RewindableGenerator;
use OriginEngine\Commands\Pipelines\HealthCheckPipeline;
use OriginEngine\Contracts\Site\SiteRepository;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Command\Command;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Pipeline\RunsPipelines;
use OriginEngine\Site\Site;

class HealthCheckFixCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'healthcheck:fix';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Syncronise your local development environment to fix any issues.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(SiteRepository $siteRepository)
    {
        if(!IO::confirm('This is a potentially destructive action, so it is recommended to ensure your work is saved and backed up/on git. Would you like to continue?', false)) {
            IO::warning('Health check did not run.');
            return 1;
        }

        foreach($this->getCheckers() as $checker) {
            IO::task('Fixing ' . $checker->checking(), function() use ($checker, $siteRepository) {
                foreach($siteRepository->all()->filter(fn(Site $site) => $checker->check($site)) as $site) {
                    $checker->checkAndFix($site);
                }
            }, 'Diagnosing...');
        }

        IO::success('Fixes complete');
    }

    /**
     * Get all checkers
     *
     * @return array|Checker[]
     */
    public function getCheckers(): RewindableGenerator
    {
        return $this->app->tagged('healthcheck');
    }

}
