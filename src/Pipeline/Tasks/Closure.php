<?php

namespace OriginEngine\Pipeline\Tasks;

use Illuminate\Support\Collection;
use OriginEngine\Helpers\Directory\Directory;
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

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        $this->writeInfo('Calling closure');

        try {
            $output = $config->get('closure')($config, $workingDirectory);
        } catch (\Exception $e) {
            $this->writeError(sprintf('[%s] at %s, line %u', $e->getMessage(), $e->getFile(), $e->getCode()));
            $this->writeDebug($e->getTraceAsString());
            return $this->failed();
        }

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

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        if($config->get('revert') !== null) {
            $config->get('revert')($config, $output);
        }
    }

}
