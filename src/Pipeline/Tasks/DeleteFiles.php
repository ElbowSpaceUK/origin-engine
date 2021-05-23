<?php

namespace OriginEngine\Pipeline\Tasks;

use Illuminate\Support\Collection;
use OriginEngine\Pipeline\Task;
use OriginEngine\Helpers\Env\EnvRepository;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;
use OriginEngine\Pipeline\TaskResponse;

class DeleteFiles extends Task
{

    /**
     * @param string $directory The directory or filename to remove, relative to the working directory. Leave null to remove entire
     * working directory.
     */
    public function __construct(string $directory = null)
    {
        parent::__construct([
            'directory' => $directory,
        ]);
    }

    protected function execute(WorkingDirectory $workingDirectory, Collection $config): TaskResponse
    {
        $path = Filesystem::append($workingDirectory->path(), $config->get('directory'));

        $this->writeInfo(sprintf('Deleting %s', $path));

        Filesystem::create()->remove($path);

        return $this->succeeded(['full-path' => $path]);
    }

    protected function undo(WorkingDirectory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        // Cannot undo
    }

    protected function upName(Collection $config): string
    {
        return sprintf('Deleting path %s', $config->get('directory', 'working directory'));
    }

    protected function downName(Collection $config): string
    {
        return sprintf('Cannot undo deleting directory %s', $config->get('directory'));
    }
}
