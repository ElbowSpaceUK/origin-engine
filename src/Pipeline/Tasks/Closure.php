<?php

namespace OriginEngine\Pipeline\Tasks;

use Illuminate\Support\Collection;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;
use OriginEngine\Pipeline\Task;

class Closure extends Task
{

    public function __construct(\Closure $closure)
    {
        parent::__construct([
            'closure' => $closure
        ]);
    }


    public function execute(WorkingDirectory $workingDirectory, Collection $config)
    {
        $this->info('Working directory is ' . $workingDirectory->path());
        $this->info('Config is ' . $config->toJson());

    }
}
