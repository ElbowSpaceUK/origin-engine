<?php

namespace OriginEngine\Commands;

use OriginEngine\Commands\Pipelines\DeleteFeature;
use OriginEngine\Contracts\Command\FeatureCommand;
use OriginEngine\Contracts\Feature\FeatureRepository;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Pipeline\RunsPipelines;

class FeatureDelete extends FeatureCommand
{
    use RunsPipelines;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'feature:delete';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Delete the given feature';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(FeatureRepository $featureRepository)
    {
        $feature = $this->getFeature('Which feature would you like to delete?');

        $history = $this->runPipeline(new DeleteFeature($feature), $feature->getSite()->getDirectory());

        if($history->allSuccessful()) {
            IO::success('Feature deleted');
        }
    }

}
