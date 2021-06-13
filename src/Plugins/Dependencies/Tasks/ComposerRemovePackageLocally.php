<?php

namespace OriginEngine\Plugins\Dependencies\Tasks;

use Illuminate\Support\Collection;
use OriginEngine\Helpers\Composer\ComposerModifier;
use OriginEngine\Helpers\Composer\ComposerReader;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Pipeline\Task;
use OriginEngine\Pipeline\TaskResponse;
use OriginEngine\Plugins\Dependencies\LocalPackage;

class ComposerRemovePackageLocally extends Task
{

    public function __construct(LocalPackage $package)
    {
        parent::__construct([
            'package' => $package,
        ]);
    }

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        $this->export('backup', Filesystem::create()->read(Filesystem::append($workingDirectory->path(), 'composer.json')));

        $package = $config->get('package');

        if($package->getType() === 'direct') {
            $this->writeDebug(sprintf('Changing the dependency %s to version %s', $package->getName(), $package->getOriginalVersion()));
            ComposerModifier::for($workingDirectory)->changeDependencyVersion($package->getName(), $package->getOriginalVersion());
        } elseif($package->getType() === 'indirect') {
            $this->writeDebug(sprintf('Changing the dependency %s as a requirement', $package->getName()));
            ComposerModifier::for($workingDirectory)->remove($package->getName());
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
        return sprintf('CHanging %s to a remote dependency', $config->get('package')->getName());
    }

    protected function downName(Collection $config): string
    {
        return 'Restoring composer.json backup';
    }
}
