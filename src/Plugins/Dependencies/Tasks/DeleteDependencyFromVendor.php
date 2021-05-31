<?php

namespace OriginEngine\Plugins\Dependencies\Tasks;

use Illuminate\Support\Collection;
use OriginEngine\Helpers\Composer\ComposerModifier;
use OriginEngine\Helpers\Composer\ComposerReader;
use OriginEngine\Helpers\Composer\ComposerRunner;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Pipeline\Task;
use OriginEngine\Pipeline\TaskResponse;

class DeleteDependencyFromVendor extends Task
{

    public function __construct(string $package)
    {
        parent::__construct([
            'package' => $package
        ]);
    }

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        $currentVendorPath = Filesystem::append($workingDirectory->path(), 'vendor', $config->get('package'));

        if(Filesystem::create()->exists($currentVendorPath)) {
            Filesystem::create()->remove($currentVendorPath);
        }

        return $this->succeeded();
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        ComposerRunner::for($workingDirectory)->update();
    }

    protected function upName(Collection $config): string
    {
        return sprintf('Removing vendor files for %s', $config->get('package'));
    }

    protected function downName(Collection $config): string
    {
        return sprintf('Restoring vendor files for %s', $config->get('package'));
    }
}
