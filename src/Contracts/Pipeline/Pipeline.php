<?php

namespace OriginEngine\Contracts\Pipeline;

use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;

interface Pipeline
{

    public function install(WorkingDirectory $directory);

    public function uninstall(WorkingDirectory $directory);

}
