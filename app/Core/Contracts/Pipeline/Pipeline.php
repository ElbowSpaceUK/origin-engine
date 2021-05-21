<?php

namespace App\Core\Contracts\Pipeline;

use App\Core\Helpers\WorkingDirectory\WorkingDirectory;

interface Pipeline
{

    public function install(WorkingDirectory $directory);

    public function uninstall(WorkingDirectory $directory);

}
