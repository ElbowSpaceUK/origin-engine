<?php

namespace OriginEngine\Plugins\Dependencies\Commands;

use OriginEngine\Contracts\Command\Command;
use OriginEngine\Contracts\Command\FeatureCommand;
use OriginEngine\Plugins\Dependencies\LocalPackage;
use OriginEngine\Plugins\Dependencies\Contracts\LocalPackageRepository;

class DepList extends FeatureCommand
{

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'dependency:list';

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
        $packages = app(LocalPackageRepository::class)->getAllThroughFeature($feature->getId());

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
