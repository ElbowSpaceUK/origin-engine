<?php

namespace OriginEngine\Pipeline\Tasks\Composer;

use Illuminate\Support\Collection;
use OriginEngine\Helpers\Composer\ComposerRunner;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Task;
use OriginEngine\Pipeline\TaskResponse;

class ComposerUpdate extends Task
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
        $output = ComposerRunner::for($workingDirectory)->withPhp($config->get('php-version', '74'))
            ->update();
        $this->export('output', $output);

        return $this->succeeded();
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        ComposerRunner::for($workingDirectory)->withPhp($config->get('php-version', '74'))
            ->install();
    }

    protected function upName(Collection $config): string
    {
        return 'Run composer update';
    }

    protected function downName(Collection $config): string
    {
        return 'Run composer install';
    }
}
