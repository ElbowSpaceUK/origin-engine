<?php

namespace App\Core\Pipeline\Tasks;

use App\Core\Contracts\Pipeline\Task;
use App\Core\Helpers\Composer\ComposerRunner;
use App\Core\Helpers\Storage\Filesystem;

class InstallComposerDependencies extends Task
{

    public function up(\App\Core\Helpers\WorkingDirectory\WorkingDirectory $workingDirectory): void
    {
        $composer = new ComposerRunner($workingDirectory);
        $composer->install();
    }

    public function down(\App\Core\Helpers\WorkingDirectory\WorkingDirectory $workingDirectory): void
    {
        Filesystem::create()->remove(
            $workingDirectory->path() . '/vendor'
        );
    }

}
