<?php


namespace OriginEngine\Helpers\Composer\Operations\Operations;


use OriginEngine\Contracts\Helpers\Composer\Operation;
use OriginEngine\Helpers\Composer\Schema\Schema\ComposerSchema;
use OriginEngine\Helpers\Composer\Schema\Schema\PackageSchema;

class RequireDev implements Operation
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
        $requireDev = $composerSchema->getRequireDev();
        $updatedRequireDev = [];
        foreach($requireDev as $package) {
            if($package->getName() === $this->name) {
                throw new \Exception(
                    sprintf('Package %s is already required as version %s', $package->getName(), $package->getVersion())
                );
            }
            $updatedRequireDev[] = $package;
        }
        $updatedRequireDev[] = new PackageSchema(
            $this->name, $this->version
        );
        $composerSchema->setRequireDev($updatedRequireDev);
        return $composerSchema;
    }
}
