<?php

namespace OriginEngine\Plugins\Dependencies\Tasks;

use Illuminate\Support\Collection;
use OriginEngine\Helpers\Composer\ComposerRunner;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Pipeline\Task;
use OriginEngine\Pipeline\TaskResponse;
use OriginEngine\Plugins\Dependencies\LocalPackage;

class MarkDependencyAsRemote extends Task
{

    public function __construct(LocalPackage $localPackage)
    {
        parent::__construct([
            'package' => $localPackage
        ]);
    }

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        $package = $config->get('package');
        $this->export('initial-status', $package->isLocal());

        $package->is_local = false;
        $package->save();

        return $this->succeeded();
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        $package = $config->get('package');
        if($output->get('initial-status') === true) {
            $package->is_local = true;
        } else {
            $package->is_local = false;
        }

        $package->save();
    }

    protected function upName(Collection $config): string
    {
        return sprintf('Marking dependency as remote');
    }

    protected function downName(Collection $config): string
    {
        return sprintf('Marking dependency as local');
    }
}
