<?php

namespace App\Commands;

use App\Core\Contracts\Command\Command;
use App\Core\Contracts\Command\FeatureCommand;
use App\Core\Contracts\Feature\FeatureRepository;
use App\Core\Helpers\IO\IO;

class FeatureDelete extends FeatureCommand
{
    protected bool $supportsDependencies = false;

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
    protected $description = 'Delete the given instance';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(FeatureRepository $featureRepository)
    {
        $feature = $this->getFeature('Which feature would you like to delete?', null, true);

        if($feature->getSite()->hasCurrentFeature() && $feature->getSite()->getCurrentFeature()->is($feature)) {
            $this->call(SiteReset::class, ['--site' => $feature->getSite()->getId()]);
        }

        $featureRepository->delete($feature->getId());

        IO::success('Feature deleted');
    }

}
