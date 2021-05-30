<?php

namespace OriginEngine\Plugins\Dependencies\Tasks;

use Illuminate\Support\Collection;
use OriginEngine\Feature\Feature;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Task;
use OriginEngine\Pipeline\TaskResponse;

class CreateLocalDependencyModel extends Task
{

    public function __construct(string $name, string $url, Feature $feature, string $branch)
    {
        parent::__construct([
            'name' => $name,
            'url' => $url,
            'feature' => $feature,
            'branch' => $branch
        ]);
    }

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        // TODO: Implement execute() method.
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        // TODO: Implement undo() method.
    }

    protected function upName(Collection $config): string
    {
        // TODO: Implement upName() method.
    }

    protected function downName(Collection $config): string
    {
        // TODO: Implement downName() method.
    }
}
