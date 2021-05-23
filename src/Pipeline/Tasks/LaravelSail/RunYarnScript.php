<?php

namespace OriginEngine\Pipeline\Tasks\LaravelSail;

use Illuminate\Support\Collection;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;
use OriginEngine\Pipeline\Task;
use OriginEngine\Helpers\Terminal\Executor;
use OriginEngine\Pipeline\TaskResponse;

class RunYarnScript extends Task
{

    /**
     * @param string|null $cwd The directory to run the command in
     */
    public function __construct(string $script, ?string $cwd = null)
    {
        parent::__construct([
            'script' => $script,
            'cwd' => $cwd
        ]);
    }

    protected function execute(WorkingDirectory $workingDirectory, Collection $config): TaskResponse
    {
        $command = './vendor/bin/sail yarn';

        if ($config->get('cwd')) {
            $command .= sprintf(' --cwd %s', $config->get('cwd'));
        }

        $command .= sprintf(' run %s --non-interactive', $config->get('script'));

        $output = Executor::cd($workingDirectory)->execute($command);
        if ($output) {
            $this->writeDebug($output);
        }
        return $this->succeeded();
    }

    protected function undo(WorkingDirectory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        // No undo action
    }

    protected function upName(Collection $config): string
    {
        return sprintf('Running Yarn script [%s].', $config->get('script'));
    }

    protected function downName(Collection $config): string
    {
        return sprintf('Script [%s] cannot be rolled back.', $config->get('script'));
    }
}

