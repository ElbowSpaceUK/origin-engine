<?php

namespace OriginEngine\Pipeline\Tasks\Files;

use Illuminate\Support\Collection;
use OriginEngine\Contracts\Setup\SetupStep;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Pipeline\Task;
use OriginEngine\Pipeline\TaskResponse;

class OverwriteFile extends Task
{

    /**
     * Full path to the file to create/overwrite
     *
     * @param string $path The path to the file to copy (relative to the working directory)
     * @param string $content The content of the file
     */
    public function __construct(string $path, string $content)
    {
        parent::__construct([
            'path' => $path,
            'content' => $content
        ]);
    }

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        $file = Filesystem::append($workingDirectory->path(), $config->get('path'));

        if(!Filesystem::create()->exists($file)) {
            Filesystem::create()->touch($file);
            $this->writeInfo('Created the file ' . $config->get('path'));
        } else {
            $this->export('current-file', Filesystem::create()->read($file));
        }

        Filesystem::create()->dumpFile($file, $config->get('content'));

        return $this->succeeded();
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        $file = Filesystem::append($workingDirectory->path(), $config->get('path'));

        if($output->has('current-file')) {
            Filesystem::create()->dumpFile($file, $output->get('current-file'));
        } else {
            Filesystem::create()->remove($file);
        }
    }

    protected function upName(Collection $config): string
    {
        return 'Adding text to file ' . $config->get('path');
    }

    protected function downName(Collection $config): string
    {
        return 'Removing text from file ' . $config->get('path');
    }
}
