<?php

namespace OriginEngine\Pipeline\Old\Tasks;

use OriginEngine\Contracts\Pipeline\Task;
use OriginEngine\Helpers\Composer\ComposerRunner;
use OriginEngine\Helpers\Storage\Filesystem;

class InstallComposerDependencies extends Task
{

    public function up(\OriginEngine\Helpers\WorkingDirectory\WorkingDirectory $workingDirectory): void
    {
        $composer = new ComposerRunner($workingDirectory);
        $composer->install();
    }

    public function down(\OriginEngine\Helpers\WorkingDirectory\WorkingDirectory $workingDirectory): void
    {
        Filesystem::create()->remove(
            $workingDirectory->path() . '/vendor'
        );
    }

}
