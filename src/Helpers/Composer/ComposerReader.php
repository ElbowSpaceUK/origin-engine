<?php


namespace OriginEngine\Helpers\Composer;


use OriginEngine\Helpers\Composer\Lock\ComposerLockReader;
use OriginEngine\Helpers\Composer\Schema\ComposerRepository;
use OriginEngine\Helpers\Composer\Schema\Schema\ComposerSchema;
use OriginEngine\Helpers\Directory\Directory;

class ComposerReader
{
    /**
     * @var ComposerLockReader
     */
    private ComposerLockReader $composerLockReader;
    /**
     * @var ComposerSchema
     */
    private ComposerSchema $composerSchema;

    public function __construct(ComposerLockReader $composerLockReader, ComposerSchema $composerSchema)
    {
        $this->composerLockReader = $composerLockReader;
        $this->composerSchema = $composerSchema;
    }

    public static function for(Directory $workingDirectory,
                               string $composerJsonName = 'composer.json',
                               string $composerLockName = 'composer.lock'): ComposerReader
    {
        $composerLockReader = app(ComposerLockReader::class, [
            'workingDirectory' => $workingDirectory,
            'lockName' => $composerLockName
        ]);
        $composerSchema = app(ComposerRepository::class)->get($workingDirectory, $composerJsonName);

        return app(static::class, [
            'composerLockReader' => $composerLockReader,
            'composerSchema' => $composerSchema
        ]);
    }

    public function isInstalled(string $package): bool
    {
        return $this->composerLockReader->getPackageSchema($package) !== null;
    }

    public function getInstalledVersion(string $package): string
    {
        if($this->isInstalled($package)) {
            return $this->composerLockReader->getPackageSchema($package)['version'];
        }
        throw new \Exception(
            sprintf('Package %s is not installed', $package)
        );
    }

    public function isDependency(string $packageName, bool $includeDev = false): bool
    {
        $required = $this->composerSchema->getRequire();
        foreach($required as $package) {
            if($package->getName() === $packageName) {
                return true;
            }
        }
        return $includeDev ? $this->isDevDependency($packageName) : false;
    }

    public function isDevDependency(string $packageName): bool
    {
        $required = $this->composerSchema->getRequireDev();
        foreach($required as $package) {
            if($package->getName() === $packageName) {
                return true;
            }
        }
        return false;
    }

}
