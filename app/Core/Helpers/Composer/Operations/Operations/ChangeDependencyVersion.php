<?php

namespace App\Core\Helpers\Composer\Operations\Operations;

use App\Core\Contracts\Helpers\Composer\Operation;
use App\Core\Helpers\Composer\Schema\Schema\ComposerSchema;
use App\Core\Helpers\Composer\Schema\Schema\PackageSchema;

class ChangeDependencyVersion implements Operation
{

    private string $name;
    private string $version;

    public function __construct(string $name, string $version)
    {
        $this->name = $name;
        $this->version = $version;
    }

    public function perform(ComposerSchema $composerSchema): ComposerSchema
    {
        $composerSchema->setRequire(
            $this->performOn(
                $composerSchema->getRequire()
            )
        );

        $composerSchema->setRequireDev(
            $this->performOn(
                $composerSchema->getRequireDev()
            )
        );

        return $composerSchema;
    }

    /**
     * @param array|PackageSchema[] $packages
     * @return array
     */
    public function performOn(array $packages)
    {
        $updated = [];
        foreach($packages as $package) {
            if($package->getName() === $this->name) {
                $package->setVersion($this->version);
            }
            $updated[] = $package;
        }
        return $updated;
    }

}
