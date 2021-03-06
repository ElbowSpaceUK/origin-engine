<?php

namespace OriginEngine\Plugins\Dependencies\Commands;

use OriginEngine\Command\Command;
use OriginEngine\Command\FeatureCommand;
use OriginEngine\Command\SiteCommand;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Plugins\Dependencies\LocalPackage;
use OriginEngine\Plugins\Dependencies\Contracts\LocalPackageRepository;

class DepList extends SiteCommand
{

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
        $feature = $this->getMainFeature('Which feature would you like to see the local packages for?');

        $packages = collect(app(LocalPackageRepository::class)->getAllThroughFeature($feature->getId()));

        $this->table(
            ['ID', 'Name', 'URL', 'Type', 'Version', 'Installed'],
            $packages->map(function(LocalPackage $dep) {
                return [
                    $dep->getId(),
                    $dep->getName(),
                    $dep->getUrl(),
                    $dep->getType(),
                    $dep->getOriginalVersion(),
                    $dep->isLocal() ? 'Y' : 'N'
                ];
            })
        );
    }
}
