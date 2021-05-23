<?php

namespace OriginEngine\Pipeline\Tasks\LaravelSail;

use Illuminate\Support\Collection;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;
use OriginEngine\Pipeline\Task;
use OriginEngine\Helpers\Terminal\Executor;
use OriginEngine\Pipeline\TaskResponse;

class InstallYarnDependencies extends Task
{

    /**
     * @param string|null $cwd The directory to run the command in
     */
    public function __construct(?string $cwd = null)
    {
        parent::__construct([
            'cwd' => $cwd
        ]);
    }

    protected function execute(WorkingDirectory $workingDirectory, Collection $config): TaskResponse
    {
        $command = './vendor/bin/sail yarn';

        if($config->get('cwd')) {
            $command .= sprintf(' --cwd %s', $config->get('cwd'));
        }

        $command .= ' install --non-interactive --no-progress';

        $output = Executor::cd($workingDirectory)->execute($command);
        if($output) {
            $this->writeDebug($output);
        }
        return $this->succeeded();
    }

    protected function undo(WorkingDirectory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        Executor::cd($workingDirectory)->execute('rm -r node_modules');
    }

    protected function upName(Collection $config): string
    {
        return 'Installing Yarn dependencies';
    }

    protected function downName(Collection $config): string
    {
        return 'Removing Yarn dependencies';
    }
}
