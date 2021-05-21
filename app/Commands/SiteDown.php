<?php

namespace App\Commands;

use App\Core\Contracts\Command\Command;
use App\Core\Contracts\Command\SiteCommand;
use App\Core\Helpers\IO\IO;
use App\Core\Helpers\Terminal\Executor;
use App\Core\Helpers\WorkingDirectory\WorkingDirectory;
use App\Core\Site\Site;

class SiteDown extends SiteCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'site:down';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Turn off the given site';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $site = $this->getSite(
            'Which site would you like to turn off?',
            fn(Site $site) => $site->getStatus() === Site::STATUS_READY
        );

        $workingDirectory = WorkingDirectory::fromSite($site);

        IO::info('Turning off site.');

        Executor::cd($workingDirectory)->execute('./vendor/bin/sail down');

        IO::success('Turned off site.');

    }

}
