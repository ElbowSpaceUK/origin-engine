<?php

namespace OriginEngine\Pipeline\Tasks;

use OriginEngine\Pipeline\Task;

class Closure extends Task
{

    private \Closure $closure;

    public function __construct(\Closure $closure)
    {
        $this->closure = $closure;
    }

}