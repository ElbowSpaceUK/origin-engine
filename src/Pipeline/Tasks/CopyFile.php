<?php

namespace OriginEngine\Pipeline\Tasks;

use Illuminate\Support\Collection;
use OriginEngine\Pipeline\Task;
use OriginEngine\Helpers\Env\EnvRepository;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;
use OriginEngine\Pipeline\Old\ProvisionedTask;
use OriginEngine\Pipeline\TaskResponse;

class CopyFile extends Task
{

    /**
     * @param string $source The file to copy
     * @param string $destination The destination of the copied file, where to copy the file to
     */
    public function __construct(string $source, string $destination)
    {
        parent::__construct([
            'source' => $source,
            'destination' => $destination
        ]);
    }

    protected function execute(WorkingDirectory $workingDirectory, Collection $config): TaskResponse
    {
        $source = Filesystem::append($workingDirectory->path(), $config->get('source'));
        $destination = Filesystem::append($workingDirectory->path(), $config->get('destination'));

        $this->writeInfo(sprintf('Copying %s to %s', $source, $destination));

        Filesystem::create()->copy(
            $source,
            $destination
        );

        return $this->succeeded([
            'full-source' => $source,
            'full-destination' => $destination
        ]);
    }

    protected function undo(WorkingDirectory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        Filesystem::create()->remove(
            Filesystem::append($workingDirectory->path(), $config->get('destination'))
        );
    }

    protected function upName(Collection $config): string
    {
        return sprintf('Copying path %s to %s', $config->get('source'), $config->get('destination'));
    }

    protected function downName(Collection $config): string
    {
        return sprintf('Removing copied file %s', $config->get('destination'));
    }
}
