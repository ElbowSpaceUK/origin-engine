<?php

namespace OriginEngine\Commands;

use OriginEngine\Contracts\Command\SiteCommand;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Pipeline\RunsPipelines;

class SiteDefault extends SiteCommand
{
    use RunsPipelines;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'site:default';

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
    public function handle()
    {
        $site = $this->getSite('Which site would you like to use by default?');

        $history = $this->runPipeline(new \OriginEngine\Commands\Pipelines\SetDefaultSite($site), $site->getDirectory());

        if($history->allSuccessful()) {
            IO::success('Default site changed');
        } else {
            IO::error('Could not change default site');
        }

    }

}
