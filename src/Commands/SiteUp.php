<?php

namespace OriginEngine\Commands;

use OriginEngine\Contracts\Command\Command;
use OriginEngine\Contracts\Command\SiteCommand;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Terminal\Executor;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;
use OriginEngine\Site\Site;

class SiteUp extends SiteCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'site:up';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Turn on the given site';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $site = $this->getSite(
            'Which site would you like to turn on?',
            fn(Site $site) => $site->getStatus() === Site::STATUS_DOWN
        );

        $workingDirectory = WorkingDirectory::fromSite($site);

        IO::info('Turning on site.');

        Executor::cd($workingDirectory)->execute('./vendor/bin/sail up -d');

        IO::success('Turned on site.');

    }

}
