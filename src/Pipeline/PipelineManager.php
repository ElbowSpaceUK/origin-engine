<?php

namespace OriginEngine\Pipeline;

use Illuminate\Support\Manager;

class PipelineManager extends Manager
{

    private array $overrides = [];

    public function getDefaultDriver()
    {
        return null;
    }

    protected function createDriver($driver)
    {
        if(isset($this->overrides[$driver])) {
            return $this->overrides[$driver]($this->container);
        }

        return parent::createDriver($driver);
    }

    public function override(string $driver, \Closure $callback)
    {
        $this->overrides[$driver] = $callback;

        return $this;
    }
}
