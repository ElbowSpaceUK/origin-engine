<?php

namespace OriginEngine\Plugins\Dependencies\Tasks;

use Illuminate\Support\Collection;
use OriginEngine\Helpers\Composer\ComposerModifier;
use OriginEngine\Helpers\Composer\ComposerReader;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Pipeline\Task;
use OriginEngine\Pipeline\TaskResponse;

class ComposerAddLocalSymlink extends Task
{

    public function __construct(string $path)
    {
        parent::__construct([
            'path' => $path
        ]);
    }

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        $this->export('backup', Filesystem::create()->read(Filesystem::append($workingDirectory->path(), 'composer.json')));

        ComposerModifier::for($workingDirectory)->addRepository(
            'path',
            $config->get('path'),
            ['symlink' => true]
        );

        return $this->succeeded();
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        Filesystem::create()->remove(
            Filesystem::append($workingDirectory->path(), 'composer.json')
        );

        file_put_contents(
            Filesystem::append($workingDirectory->path(), 'composer.json'),
            $output->get('backup'),
        );
    }

    protected function upName(Collection $config): string
    {
        return sprintf('Adding %s as a local symlink', $config->get('path'));
    }

    protected function downName(Collection $config): string
    {
        return sprintf('Removing %s as a local symlink', $config->get('path'));
    }
}
