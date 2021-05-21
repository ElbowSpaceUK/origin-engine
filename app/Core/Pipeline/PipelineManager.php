<?php

namespace App\Core\Pipeline;

use Illuminate\Support\Manager;

class PipelineManager extends Manager
{

    public function getDefaultDriver()
    {
        return null;
    }
}
