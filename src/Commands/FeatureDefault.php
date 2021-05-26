<?php

namespace OriginEngine\Commands;

use OriginEngine\Commands\Pipelines\FeatureDefault as FeatureDefaultPipeline;
use OriginEngine\Contracts\Command\FeatureCommand;
use OriginEngine\Contracts\Feature\FeatureResolver;
use OriginEngine\Contracts\Pipeline\PipelineRunner;
use OriginEngine\Helpers\IO\IO;

class FeatureDefault extends FeatureCommand
{

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'feature:default';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Use the given feature by default.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(FeatureResolver $featureResolver, PipelineRunner $pipelineRunner)
    {
        $feature = $this->getFeature('Which feature would you like to use by default?');

        // TODO way to resolve the correct pipeline, like the pipeline manager but custom made.
        $pipeline = new FeatureDefaultPipeline($feature);
        $history = $pipelineRunner->run($pipeline, $this->getPipelineConfig(), $feature->getDirectory());
        if($history->allSuccessful()) {
            IO::success('Default feature changed');
        } else {
            IO::error('Could not change default feature');
        }
    }

}
