<?php

namespace OriginEngine\Pipeline\Tasks\Utils;

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
            $output = $config->get('closure')($workingDirectory, $config);
        } catch (\Exception $e) {
            $this->writeError(sprintf('[%s] at %s, line %u', $e->getMessage(), $e->getFile(), $e->getCode()));
            $this->writeDebug($e->getTraceAsString());
            return $this->failed();
        }

        if((is_object($output) && method_exists($output, '__toString')) || $output === null || is_scalar($output)) {
            $this->writeDebug('Closure returns ' . $output);
        }

        $this->export('output', $output);

        if($output instanceof TaskResponse) {
            return $output;
        }
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
            $config->get('revert')($config, $output, $workingDirectory);
        }
    }

}
