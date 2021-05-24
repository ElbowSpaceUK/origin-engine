<?php

namespace OriginEngine\Contracts\Pipeline;

use OriginEngine\Helpers\Directory\Directory;

interface Pipeline
{

    public function install(Directory $directory);

    public function uninstall(Directory $directory);

}
