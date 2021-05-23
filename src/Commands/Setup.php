<?php

namespace OriginEngine\Commands;

use OriginEngine\Contracts\Command\Command;
use OriginEngine\Setup\SetupManager;

class Setup extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'setup';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Set up all the dependencies for Atlas to run';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(SetupManager $setupManager)
    {
        $setupManager->setup();
    }
}