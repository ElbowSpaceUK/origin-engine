<?php

namespace OriginEngine\Pipeline\Tasks\Files;

use Illuminate\Support\Collection;
use OriginEngine\Pipeline\Task;
use OriginEngine\Helpers\Env\EnvRepository;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Old\ProvisionedTask;
use OriginEngine\Pipeline\TaskResponse;

class CopyFile extends Task
{

    /**
     * @param string $source The file to copy
     * @param string $destination The destination of the copied file, where to copy the file to
     * @param bool $sourceRelativeToProject Whether the source file is relative to the working directory or not
     * @param bool $destRelativeToProject Whether the destination file is relative to the working directory or not
     */
    public function __construct(string $source, string $destination, bool $sourceRelativeToProject = true, bool $destRelativeToProject = true)
    {
        parent::__construct([
            'source' => $source,
            'destination' => $destination,
            'source-relative-to-project' => $sourceRelativeToProject,
            'destination-relative-to-project' => $destRelativeToProject
        ]);
    }

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        $source = $config->get('source-relative-to-project')
            ? Filesystem::append($workingDirectory->path(), $config->get('source'))
            : $config->get('source');

        $destination = $config->get('destination-relative-to-project')
            ? Filesystem::append($workingDirectory->path(), $config->get('destination'))
            : $config->get('destination');

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

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
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
