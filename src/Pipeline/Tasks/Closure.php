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
        $this->writeInfo('Calling closure');

        $output = $config->get('closure')($config, $workingDirectory);

        $this->writeDebug('Closure returns ' . $output);
        $this->export('output', $output);

        return $this->succeeded();
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
