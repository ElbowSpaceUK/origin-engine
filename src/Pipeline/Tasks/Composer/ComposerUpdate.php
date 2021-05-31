<?php

namespace OriginEngine\Pipeline\Tasks\Composer;

use Illuminate\Support\Collection;
use OriginEngine\Helpers\Composer\ComposerRunner;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Task;
use OriginEngine\Pipeline\TaskResponse;

class ComposerUpdate extends Task
{

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        $output = ComposerRunner::for($workingDirectory)->update();
        $this->export('output', $output);

        return $this->succeeded();
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        ComposerRunner::for($workingDirectory)->install();
    }

    protected function upName(Collection $config): string
    {
        return 'Run composer update';
    }

    protected function downName(Collection $config): string
    {
        return 'Run composer install';
    }
}
