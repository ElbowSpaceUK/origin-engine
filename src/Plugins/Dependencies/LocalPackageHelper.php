<?php

namespace OriginEngine\Plugins\Dependencies;

use OriginEngine\Helpers\Composer\ComposerModifier;
use OriginEngine\Helpers\Composer\ComposerReader;
use OriginEngine\Helpers\Composer\ComposerRunner;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Helpers\Directory\Directory;
use Cz\Git\GitException;
use Cz\Git\GitRepository;

class LocalPackageHelper
{

    public function makeRemote(LocalPackage $localPackage, Directory $workingDirectory)
    {
        $relativeInstallPath = sprintf('repos/%s', $localPackage->getName());
        $installPath = Filesystem::append(
            $workingDirectory->path(),
            $relativeInstallPath
        );

        try {
            IO::task('Scanning for changes', fn() => $this->confirmChangesSaved(Directory::fromFullPath($installPath)));
        } catch (\Exception $e) {
            IO::error($e->getMessage());
            return;
        }
        IO::task('Removing remote symlink', fn() => $this->removeSymlinkInComposer($workingDirectory, $relativeInstallPath));
        IO::task('Modify composer.json', fn() => $this->composerRequireRemote($workingDirectory, $localPackage));
        IO::task('Remove the local repository', fn() => $this->removeRepository($installPath));
        IO::task('Clearing stale dependencies', fn() => $this->clearStaleDependencies($workingDirectory, $localPackage->getName()));
        IO::task('Updating composer', fn() => $this->updateComposer($workingDirectory));
    }

    private function clearStaleDependencies(Directory $workingDirectory, string $package)
    {
        $currentVendorPath = Filesystem::append($workingDirectory->path(), 'vendor', $package);
        if(Filesystem::create()->exists($currentVendorPath)) {
            Filesystem::create()->remove($currentVendorPath);
        }
        return true;
    }

    private function updateComposer(Directory $workingDirectory)
    {
        ComposerRunner::for($workingDirectory)->update();
        return true;
    }

    private function composerRequireRemote(Directory $workingDirectory, LocalPackage $localPackage)
    {
        if($localPackage->getType() === 'direct') {
            ComposerModifier::for($workingDirectory)->changeDependencyVersion($localPackage->getName(), $localPackage->getOriginalVersion());
        } elseif($localPackage->getType() === 'indirect') {
            ComposerModifier::for($workingDirectory)->remove($localPackage->getName());
        }
        return true;
    }

    private function removeSymlinkInComposer(Directory $workingDirectory, string $relativeInstallPath)
    {
        ComposerModifier::for($workingDirectory)->removeRepository(
            'path',
            sprintf('./%s', $relativeInstallPath),
            ['symlink' => true]
        );
        return true;
    }

    private function confirmChangesSaved(Directory $workingDirectory)
    {
        if(!IO::confirm(
            sprintf(
                'Please make sure you have checked for any changes. You will lose any unpushed work in [%s] by continuing. Do you wish to continue?',
                $workingDirectory->path()
            )
        )) {
            throw new \Exception('Repository is still installed locally.');
        }
        return true;
    }

    private function removeRepository(string $installPath)
    {
        Filesystem::create()->remove($installPath);
        return true;
    }

}
