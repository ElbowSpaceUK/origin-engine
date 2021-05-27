<?php

namespace OriginEngine\Commands;

use OriginEngine\Contracts\Command\Command;
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

        if($feature->getSite()->hasCurrentFeature() && $feature->getSite()->getCurrentFeature()->is($feature)) {
            $this->call(SiteReset::class, ['--site' => $feature->getSite()->getId()]);
        }

        $featureRepository->delete($feature->getId());

        IO::success('Feature deleted');
    }

}
