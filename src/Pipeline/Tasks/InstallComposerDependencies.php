<?php

namespace OriginEngine\Pipeline\Tasks;

use Illuminate\Support\Collection;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;
use OriginEngine\Pipeline\Task;
use OriginEngine\Helpers\Composer\ComposerRunner;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Pipeline\TaskResponse;

class InstallComposerDependencies extends Task
{

    protected function execute(WorkingDirectory $workingDirectory, Collection $config): TaskResponse
    {
        $composer = new ComposerRunner($workingDirectory);
        $composer->install();

        return $this->succeeded();
    }

    protected function undo(WorkingDirectory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        Filesystem::create()->remove(
            $workingDirectory->path() . '/vendor'
        );
    }

    protected function upName(Collection $config): string
    {
        return 'Installing Composer dependencies';
    }

    protected function downName(Collection $config): string
    {
        return 'Removing Composer dependencies';
    }
}
