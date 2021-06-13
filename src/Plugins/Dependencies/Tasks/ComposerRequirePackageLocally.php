<?php

namespace OriginEngine\Plugins\Dependencies\Tasks;

use Illuminate\Support\Collection;
use OriginEngine\Helpers\Composer\ComposerModifier;
use OriginEngine\Helpers\Composer\ComposerReader;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Pipeline\Task;
use OriginEngine\Pipeline\TaskResponse;

class ComposerRequirePackageLocally extends Task
{

    public function __construct(string $package, string $branchName)
    {
        parent::__construct([
            'package' => $package,
            'branch-name' => $branchName
        ]);
    }

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        $this->export('backup', Filesystem::create()->read(Filesystem::append($workingDirectory->path(), 'composer.json')));

        $package = $config->get('package');
        $reader = ComposerReader::for($workingDirectory);

        try {
            $currentlyInstalled = ComposerReader::for($workingDirectory)->getInstalledVersion($package);
        } catch (\Exception $e) {
            return $this->succeeded();
        }

        $newVersion = sprintf('dev-%s as %s', $config->get('branch-name'), $currentlyInstalled);

        if($reader->isDependency($package, true)) {
            ComposerModifier::for($workingDirectory)->changeDependencyVersion($package, $newVersion);
        } elseif($reader->isInstalled($package)) {
            ComposerModifier::for($workingDirectory)->requireDev($package, $newVersion);
        }

        return $this->succeeded();
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        Filesystem::create()->remove(
            Filesystem::append($workingDirectory->path(), 'composer.json')
        );

        file_put_contents(
            Filesystem::append($workingDirectory->path(), 'composer.json'),
            $output->get('backup'),
        );
    }

    protected function upName(Collection $config): string
    {
        return sprintf('Requiring %s as a local dependency', $config->get('package'));
    }

    protected function downName(Collection $config): string
    {
        return 'Restoring composer.json backup';
    }
}
