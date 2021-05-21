<?php

namespace App\Commands;

use App\Core\Contracts\Command\Command;
use App\Core\Contracts\Command\FeatureCommand;
use App\Core\Packages\LocalPackage;

class DepList extends FeatureCommand
{

    protected bool $supportsDependencies = false;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'dep:list';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'See all local packages for the current feature.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $feature = $this->getFeature('Which feature would you like to see the local packages for?');
        $packages = $feature->getLocalPackages();

        $this->table(
            ['ID', 'Name', 'URL', 'Type', 'Version'],
            $packages->map(function(LocalPackage $dep) {
                return [
                    $dep->getId(),
                    $dep->getName(),
                    $dep->getUrl(),
                    $dep->getType(),
                    $dep->getOriginalVersion()
                ];
            })
        );
    }
}
