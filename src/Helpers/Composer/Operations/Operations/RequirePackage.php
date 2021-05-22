<?php


namespace OriginEngine\Helpers\Composer\Operations\Operations;


use OriginEngine\Contracts\Helpers\Composer\Operation;
use OriginEngine\Helpers\Composer\Schema\Schema\ComposerSchema;
use OriginEngine\Helpers\Composer\Schema\Schema\PackageSchema;

class RequirePackage implements Operation
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
        $require = $composerSchema->getRequire();
        $updatedRequire = [];
        foreach($require as $package) {
            if($package->getName() === $this->name) {
                throw new \Exception(
                    sprintf('Package %s is already required as version %s', $package->getName(), $package->getVersion())
                );
            }
            $updatedRequire[] = $package;
        }
        $updatedRequire[] = new PackageSchema(
            $this->name, $this->version
        );
        $composerSchema->setRequire($updatedRequire);
        return $composerSchema;
    }
}
