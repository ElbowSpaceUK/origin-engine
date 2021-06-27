<?php

namespace OriginEngine\Pipeline\Tasks\LaravelSail;

use Illuminate\Support\Collection;
use OriginEngine\Pipeline\Task;
use OriginEngine\Helpers\Terminal\Executor;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\TaskResponse;

class SeedLaravel extends Task
{

    /**
     * @param string $environment The environment to migrate
     */
    public function __construct(string $class, string $environment)
    {
        parent::__construct([
            'class' => $class,
            'environment' => $environment
        ]);
    }

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        $output = Executor::cd($workingDirectory)->execute(sprintf(
            './vendor/bin/sail artisan db:seed --class=%s --env=%s',
            $config->get('class'),
            $config->get('environment'),
        ));
        $this->writeDebug(sprintf('db:seed output: %s', $output));

        return $this->succeeded();
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        // Unable to undo a seed action
    }

    protected function upName(Collection $config): string
    {
        return 'Seeding Laravel';
    }

    protected function downName(Collection $config): string
    {
        return sprintf('Seeding cannot be rolled back');
    }

}
