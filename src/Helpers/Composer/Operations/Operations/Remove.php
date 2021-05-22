<?php


namespace OriginEngine\Helpers\Composer\Operations\Operations;


use OriginEngine\Contracts\Helpers\Composer\Operation;
use OriginEngine\Helpers\Composer\Schema\Schema\ComposerSchema;
use OriginEngine\Helpers\Composer\Schema\Schema\PackageSchema;

class Remove implements Operation
{

    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
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
    private function performOn(array $packages)
    {
        $updated = [];
        foreach($packages as $package) {
            if($package->getName() === $this->name) {
                continue;
            }
            $updated[] = $package;
        }
        return $updated;
    }
}
