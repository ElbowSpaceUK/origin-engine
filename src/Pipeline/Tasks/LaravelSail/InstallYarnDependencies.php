<?php

namespace OriginEngine\Pipeline\Tasks\LaravelSail;

use Illuminate\Support\Collection;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Task;
use OriginEngine\Helpers\Terminal\Executor;
use OriginEngine\Pipeline\TaskResponse;

class InstallYarnDependencies extends Task
{

    /**
     * @param string|null $cwd The directory to run the command in
     */
    public function __construct(?string $cwd = null, ?bool $valet = null)
    {
        parent::__construct([
            'cwd' => $cwd,
            'valet' => $valet
        ]);
    }

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        $command = './vendor/bin/sail yarn';

        if ($config->get('valet')) {
            $command = 'yarn';
        }

        if ($config->get('cwd')) {
            $command .= sprintf(' --cwd %s', $config->get('cwd'));
        }

        $command .= ' install --non-interactive --no-progress';

        $this->writeInfo('Running command ' . $command);

        $output = Executor::cd($workingDirectory)->execute($command);
        $this->writeDebug('yarn install output: ' . $output);

        return $this->succeeded();
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        Executor::cd($workingDirectory)->execute(sprintf('rm -r %s', Filesystem::append($config->get('cwd', $workingDirectory->path()), 'node_modules')));
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
