<?php

namespace App\Commands;

use App\Core\Contracts\Command\Command;
use App\Core\Contracts\Command\SiteCommand;
use App\Core\Helpers\IO\IO;
use App\Core\Helpers\Terminal\Executor;
use App\Core\Helpers\WorkingDirectory\WorkingDirectory;
use App\Core\Site\Site;

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
