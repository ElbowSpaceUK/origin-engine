<?php

namespace OriginEngine\Pipeline\Tasks\LaravelSail;

use Illuminate\Support\Collection;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Task;
use OriginEngine\Helpers\Composer\ComposerRunner;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Pipeline\TaskResponse;

class InstallComposerDependencies extends Task
{

    /**
     * ComposerUpdate constructor.
     * @param string $phpVersion One of '74' or '80'
     */
    public function __construct(string $phpVersion = '74')
    {
        parent::__construct([
            'php-version' => $phpVersion
        ]);
    }

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        $composer = new ComposerRunner($workingDirectory);
        $output = $composer->withPhp($config->get('php-version', '74'))->install();

        $this->writeSuccess('Ran composer install');
        $this->writeDebug('Composer install output: ' . $output);

        return $this->succeeded();
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        Filesystem::create()->remove(
            $workingDirectory->path() . '/vendor'
        );
    }

    protected function upName(Collection $config): string
    {
        return 'Installing Composer dependencies';
    }

    protected function downName(Collection $config): string
    {
        return 'Removing Composer dependencies';
    }
}
