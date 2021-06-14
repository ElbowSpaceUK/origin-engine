<?php

namespace OriginEngine\Pipeline\Tasks\Files;

use Illuminate\Support\Collection;
use OriginEngine\Contracts\Setup\SetupStep;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Pipeline\Task;
use OriginEngine\Pipeline\TaskResponse;

class CreateEmptyFile extends Task
{

    /**
     * Full path to the file to create
     *
     * @param string $path
     * @param bool $failIfExists If true, throws an exception if the file already exists
     */
    public function __construct(string $path, bool $failIfExists = true)
    {
        parent::__construct([
            'path' => $path,
            'fail-if-exists' => $failIfExists
        ]);
    }

    private function path(): string
    {
        return Filesystem::database(sprintf('%s.sqlite', config()->get('database.name', 'origin')));
    }

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        if(Filesystem::create()->exists($config->get('path'))) {
            if($config->get('fail-if-exists', true)) {
                throw new \Exception(sprintf('Path %s already exists', $config->get('path')));
            }
        } else {
            Filesystem::create()->touch($config->get('path'));

            $this->writeSuccess('Created the file ' . $config->get('path'));
        }


        return $this->succeeded();
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        Filesystem::create()->remove($config->get('path'));
    }

    protected function upName(Collection $config): string
    {
        return 'Creating file ' . $config->get('path');
    }

    protected function downName(Collection $config): string
    {
        return 'Removing file ' . $config->get('path');
    }
}
