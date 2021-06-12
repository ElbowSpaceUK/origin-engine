<?php

namespace OriginEngine\Pipeline\Tasks\LaravelSail;

use Illuminate\Support\Collection;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Helpers\Terminal\Executor;
use OriginEngine\Pipeline\Task;
use OriginEngine\Pipeline\TaskResponse;

class NewLaravelInstance extends Task
{

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        $output = Executor::cd(Directory::fromFullPath($workingDirectory->getPathWithoutBasename()))->execute(
            sprintf('curl -s https://laravel.build/%s | bash', $workingDirectory->getPathBasename())
        );

        if($output) {
            $this->writeDebug($output);
        }

        return $this->succeeded();
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        Filesystem::create()->remove($workingDirectory->path());
    }

    protected function upName(Collection $config): string
    {
        return 'Creating a new Laravel instance';
    }

    protected function downName(Collection $config): string
    {
        return 'Removing the Laravel instance';
    }
}
