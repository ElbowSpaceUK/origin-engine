<?php

namespace OriginEngine\Pipeline\Tasks\Files;

use Illuminate\Support\Collection;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Pipeline\Task;
use OriginEngine\Pipeline\TaskResponse;

class CreateDirectory extends Task
{
    /**
     * Full path to the directory to create
     *
     * @param string $path
     * @param bool $failIfExists If true, throws an exception if the directory already exists
     */
    public function __construct(string $path, bool $failIfExists = true)
    {
        parent::__construct([
            'path' => $path,
            'fail-if-exists' => $failIfExists
        ]);
    }

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        if(Filesystem::create()->exists($config->get('path'))) {
            if($config->get('fail-if-exists', true)) {
                throw new \Exception(sprintf('Path %s already exists', $config->get('path')));
            }
        } else {
            Filesystem::create()->mkdir($config->get('path'));

            $this->writeSuccess('Created the directory ' . $config->get('path'));
        }

        return $this->succeeded();
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        Filesystem::create()->remove($config->get('path'));
    }

    protected function upName(Collection $config): string
    {
        return 'Creating directory ' . $config->get('path');
    }

    protected function downName(Collection $config): string
    {
        return 'Removing directory ' . $config->get('path');
    }
}
