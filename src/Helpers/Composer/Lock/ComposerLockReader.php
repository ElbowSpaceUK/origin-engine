<?php

namespace OriginEngine\Helpers\Composer\Lock;

use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Helpers\Directory\Directory;

class ComposerLockReader
{

    /**
     * @var Directory
     */
    private Directory $workingDirectory;
    private string $lockName;

    public function __construct(Directory $workingDirectory, string $lockName = 'composer.lock')
    {
        $this->workingDirectory = $workingDirectory;
        $this->lockName = $lockName;
    }

    public function getSchema(): array
    {
        $composerLockPath = Filesystem::append(
            $this->workingDirectory->path(),
            $this->lockName
        );
        if(!Filesystem::create()->exists($composerLockPath)) {
            throw new \Exception(
                sprintf('The %s file does not exist. Please install composer dependencies.', $this->lockName)
            );
        }
        $composerLock = Filesystem::create()->read(
            $composerLockPath
        );

        return json_decode($composerLock,true);
    }

    public function getAllPackages(): array
    {
        $schema = $this->getSchema();
        if(!array_key_exists('packages', $schema)) {
            throw new \Exception(
                sprintf('The %s schema does not contain a list of packages.', $this->lockName)
            );
        }

        $packages = $schema['packages'];
        if(array_key_exists('packages-dev', $schema)) {
            $packages = array_merge($packages, $schema['packages-dev']);
        }

        return $packages;
    }

    public function getPackageSchema(string $package): ?array
    {
        $matchingPackages = array_values(
            array_filter(
                $this->getAllPackages(),
                fn($installedPackage) => array_key_exists('name', $installedPackage) && $installedPackage['name'] === $package
            )
        );
        $packageCount = count($matchingPackages);
        if($packageCount === 1) {
            return $matchingPackages[0];
        }
        if($packageCount === 0) {
            return null;
        }
        throw new \Exception(
            sprintf('Found %u packages in composer.json, 1 expected.', $packageCount)
        );
    }

}
