<?php

namespace OriginEngine\Pipeline\Tasks\Utils;

use Illuminate\Support\Collection;
use OriginEngine\Contracts\Pipeline\PipelineDownRunner;
use OriginEngine\Contracts\Pipeline\PipelineRunner;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Pipeline;
use OriginEngine\Pipeline\PipelineConfig;
use OriginEngine\Pipeline\Task;
use OriginEngine\Pipeline\TaskResponse;

class RunPipeline extends Task
{

    /**
     * @param Pipeline $pipeline The pipeline class to run
     * @param array $configuration An array of configuration to pass to the pipeline. By default, no extra config is passed in.
     */
    public function __construct(Pipeline $pipeline, array $configuration = [])
    {
        parent::__construct([
            'pipeline' => $pipeline,
            'configuration' => $configuration
        ]);
    }

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        $this->writeInfo('Running the pipeline ' . $config->get('pipeline')->getAlias());

        /** @var PipelineRunner $runner */
        $runner = app(PipelineRunner::class);

        $history = $runner->run($config->get('pipeline'), new PipelineConfig($config->get('configuration')), $workingDirectory);
        $this->export('history', $history);

        if($history->allSuccessful()) {
            return $this->succeeded();
        }
        return $this->failed();
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        $startFrom = null;

        $history = $output->get('history');
        if(!$history->allSuccessful()) {
            foreach($history->getRunTasks() as $task) {
                if($history->failed($task)) {
                    $startFrom = $task;
                }
            }
        }

        $runner = app(PipelineDownRunner::class);
        $runner->run($config->get('pipeline'), $workingDirectory, $output->get('history'), $startFrom);
    }

    protected function upName(Collection $config): string
    {
        return 'Running pipeline';
    }

    protected function downName(Collection $config): string
    {
        return 'Rolling back pipeline';
    }
}
