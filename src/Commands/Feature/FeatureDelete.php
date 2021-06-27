<?php

namespace OriginEngine\Commands\Feature;

use OriginEngine\Commands\Pipelines\DeleteFeature;
use OriginEngine\Command\SiteCommand;
use OriginEngine\Contracts\Feature\FeatureRepository;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Pipeline\RunsPipelines;

class FeatureDelete extends SiteCommand
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
    public function handle()
    {
        $feature = $this->getFeature('Which feature would you like to delete?');

        $history = $this->runPipeline(new DeleteFeature($feature), $feature->getSite()->getDirectory());

        if($history->allSuccessful()) {
            IO::success('Feature deleted');
        }
    }

}
