<?php

namespace App\Core\Packages;

use App\Core\Helpers\Composer\ComposerModifier;
use App\Core\Helpers\Composer\ComposerReader;
use App\Core\Helpers\Composer\ComposerRunner;
use App\Core\Helpers\IO\IO;
use App\Core\Helpers\Storage\Filesystem;
use App\Core\Helpers\WorkingDirectory\WorkingDirectory;
use Cz\Git\GitException;
use Cz\Git\GitRepository;

class LocalPackageHelper
{

    // TODO implement this properly with a pipeline

    public function makeLocal(LocalPackage $localPackage, WorkingDirectory $workingDirectory)
    {
        $relativeInstallPath = sprintf('repos/%s', $localPackage->getName());
        $installPath = Filesystem::append(
            $workingDirectory->path(),
            $relativeInstallPath
        );

        IO::task('Clone the repository', fn() => $this->cloneRepository($installPath, $localPackage->getUrl()));
        IO::task(sprintf('Checkout branch %s', $localPackage->getBranch()), fn() => $this->checkoutBranch($localPackage->getBranch(), $installPath));
        IO::task('Modify composer.json', fn() => $this->composerRequireLocal($workingDirectory, $localPackage->getName(), $localPackage->getBranch()));
        IO::task('Adding local symlink', fn() => $this->addSymlinkInComposer($workingDirectory, $relativeInstallPath));
        IO::task('Clearing stale dependencies', fn() => $this->clearStaleDependencies($workingDirectory, $localPackage->getName()));
        IO::task('Updating composer', fn() => $this->updateComposer($workingDirectory));
    }

    public function makeRemote(LocalPackage $localPackage, WorkingDirectory $workingDirectory)
    {
        $relativeInstallPath = sprintf('repos/%s', $localPackage->getName());
        $installPath = Filesystem::append(
            $workingDirectory->path(),
            $relativeInstallPath
        );

        try {
            IO::task('Scanning for changes', fn() => $this->confirmChangesSaved(WorkingDirectory::fromPath($installPath)));
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

    private function cloneRepository(string $installPath, string $repositoryUrl)
    {
        if(!Filesystem::create()->exists($installPath)) {
            GitRepository::cloneRepository($repositoryUrl, $installPath);
        }
        return true;
    }

    private function checkoutBranch(string $branchName, string $installPath)
    {
        $git = new GitRepository($installPath);
        try {
            $git->checkout($branchName);
        } catch (GitException $e) {
            $git->createBranch($branchName, true);
        }
        return true;
    }

    private function composerRequireLocal(WorkingDirectory $workingDirectory, string $package, string $branchName)
    {
        $reader = ComposerReader::for($workingDirectory);
        try {
            $currentlyInstalled = ComposerReader::for($workingDirectory)->getInstalledVersion($package);
        } catch (\Exception $e) {
            return true;
        }
        $newVersion = sprintf('dev-%s as %s', $branchName, $currentlyInstalled);

        if($reader->isDependency($package, true)) {
            ComposerModifier::for($workingDirectory)->changeDependencyVersion($package, $newVersion);
        } elseif($reader->isInstalled($package)) {
            ComposerModifier::for($workingDirectory)->requireDev($package, $newVersion);
        }
        return true;
    }

    private function addSymlinkInComposer(WorkingDirectory $workingDirectory, string $relativeInstallPath)
    {
        ComposerModifier::for($workingDirectory)->addRepository(
            'path',
            sprintf('./%s', $relativeInstallPath),
            ['symlink' => true]
        );
        return true;
    }

    private function clearStaleDependencies(WorkingDirectory $workingDirectory, string $package)
    {
        $currentVendorPath = Filesystem::append($workingDirectory->path(), 'vendor', $package);
        if(Filesystem::create()->exists($currentVendorPath)) {
            Filesystem::create()->remove($currentVendorPath);
        }
        return true;
    }

    private function updateComposer(WorkingDirectory $workingDirectory)
    {
        ComposerRunner::for($workingDirectory)->update();
        return true;
    }

    private function composerRequireRemote(WorkingDirectory $workingDirectory, LocalPackage $localPackage)
    {
        if($localPackage->getType() === 'direct') {
            ComposerModifier::for($workingDirectory)->changeDependencyVersion($localPackage->getName(), $localPackage->getOriginalVersion());
        } elseif($localPackage->getType() === 'indirect') {
            ComposerModifier::for($workingDirectory)->remove($localPackage->getName());
        }
        return true;
    }

    private function removeSymlinkInComposer(WorkingDirectory $workingDirectory, string $relativeInstallPath)
    {
        ComposerModifier::for($workingDirectory)->removeRepository(
            'path',
            sprintf('./%s', $relativeInstallPath),
            ['symlink' => true]
        );
        return true;
    }

    private function confirmChangesSaved(WorkingDirectory $workingDirectory)
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
