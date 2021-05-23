<?php

namespace OriginEngine\Pipeline\Tasks;

use Illuminate\Support\Collection;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;
use OriginEngine\Pipeline\Task;
use OriginEngine\Pipeline\TaskResponse;

class Closure extends Task
{

    public function __construct(\Closure $closure, \Closure $revert = null)
    {
        parent::__construct([
            'closure' => $closure,
            'revert' => $revert
        ]);
    }

    protected function execute(WorkingDirectory $workingDirectory, Collection $config): TaskResponse
    {
        $this->writeInfo('Working directory is ' . $workingDirectory->path());
        $this->writeInfo('Config is ' . $config->toJson());

        // TODO Use reflection to determine what to pass to the callback?
        $config->get('closure')($config, $workingDirectory);

        return $this->succeeded([
            'id' => 12,
            'output' => 743
        ]);
    }

    protected function upName(Collection $config): string
    {
        return 'Running callback';
    }

    protected function downName(Collection $config): string
    {
        return 'Running reverse callback';
    }

    protected function undo(WorkingDirectory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        if($config->get('revert') !== null) {
            $config->get('revert')($config, $output);
        }
    }

}
