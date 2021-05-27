<?php

namespace OriginEngine\Commands;

use OriginEngine\Commands\Pipelines\FeatureDefault as FeatureDefaultPipeline;
use OriginEngine\Contracts\Command\FeatureCommand;
use OriginEngine\Contracts\Feature\FeatureResolver;
use OriginEngine\Contracts\Pipeline\PipelineRunner;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Pipeline\RunsPipelines;

class FeatureDefault extends FeatureCommand
{
    use RunsPipelines;

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
    public function handle()
    {
        $feature = $this->getFeature('Which feature would you like to use by default?');

        $history = $this->runPipeline(new FeatureDefaultPipeline($feature), $feature->getDirectory());
        if($history->allSuccessful()) {
            IO::success('Default feature changed');
        } else {
            IO::error('Could not change default feature');
        }
    }

}
