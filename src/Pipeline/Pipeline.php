<?php

namespace OriginEngine\Pipeline;

abstract class Pipeline
{

    /**
     * @return array|Task[]
     */
    public function getTasks(): array
    {
        return [];
    }

    public function run()
    {
        
    }

}