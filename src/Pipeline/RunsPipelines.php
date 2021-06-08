<?php

namespace OriginEngine\Pipeline;

use Illuminate\Support\Collection;
use OriginEngine\Contracts\Pipeline\PipelineRunner;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Helpers\IO\IO;
use Symfony\Component\Console\Input\InputOption;

trait RunsPipelines
{

    public function configureRunPipelines()
    {
        if(!is_callable([$this, 'addOption'])) {
            throw new \Exception('Could not configure the pipeline. Please use this trait in the context of a command.');
        }
        $this->addOption('config', 'C', InputOption::VALUE_IS_ARRAY|InputOption::VALUE_OPTIONAL, 'Data to pass to the pipeline. Separate the variable and value with an equals.', []);
    }

    /**
     * Get the ID unique to this pipeline. This should normally be the name of the command,
     * and is used to allow people to override your pipeline.
     *
     * @return string
     */
    public function getPipelineRunId(): string
    {
        if(!is_callable([$this, 'getName'])) {
            throw new \Exception('Could not determine a name for the pipeline. Please implement the `getPipelineRunId` method in your command.');
        }

        return $this->getName();
    }

    private function getConfig(): PipelineConfig
    {
        return new PipelineConfig($this->getConfigInput());
    }

    public function getConfigInput(): array
    {
        if(!method_exists($this, 'option')) {
            throw new \Exception('Could not determine the pipeline config. Please use this trait in the context of a command.');
        }

        return collect($this->option('config', []))->mapWithKeys(function($data) {
            $parts = explode('=', $data);
            if(count($parts) !== 2) {
                throw new \Exception(sprintf('Data [%s] could not be parsed, please ensure you include both the variable name and value separated with an =.', $data));
            }
            return [$parts[0] => $parts[1]];
        })->toArray();
    }

    /**
     * @param Pipeline $pipeline The pipeline to run
     * @param Directory $directory The directory to run the pipeline in
     * @param string|null $pipelineId A unique ID for the pipeline. Defaults to the command name if being used in a command.
     * @param \Closure|null $modifyConfig A callback to modify any configuration before the pipeline is ran
     *
     * @return PipelineHistory
     * @throws \Exception
     */
    public function runPipeline(Pipeline $pipeline, Directory $directory, ?string $pipelineId = null, \Closure $modifyConfig = null): PipelineHistory
    {
        $pipeline->setAlias($pipelineId ?? $this->getPipelineRunId());

        $config = $this->getConfig();
        if($modifyConfig !== null) {
            $modifyConfig($config);
        }

        return $this->getPipelineRunner()->run($pipeline, $config, $directory);
    }

    public function getPipelineRunner(): PipelineRunner
    {
        return app(PipelineRunner::class);
    }

}
