<?php

namespace OriginEngine\Plugins\Dependencies\Pipelines;

use OriginEngine\Feature\Feature;
use OriginEngine\Pipeline\Pipeline;
use OriginEngine\Pipeline\Task;
use OriginEngine\Plugins\Dependencies\LocalPackage;

class MakeExistingDependencyLocal extends Pipeline
{


    private LocalPackage $localPackage;

    public function __construct(LocalPackage $localPackage)
    {
        $this->localPackage = $localPackage;
    }

    protected function tasks(): array
    {
        return [
            'clone-repository' => '',
            'checkout-branch' => '',
            'modify-composer-json' => '',
            'add-local-symlink' => '',
            'clear-stale-dependencies' => '',
            'update-composer' => ''
        ];
    }
}
