<?php

namespace OriginEngine\Commands;

use OriginEngine\Contracts\Feature\FeatureRepository;
use OriginEngine\Feature\Feature;
use OriginEngine\Contracts\Command\Command;

class FeatureList extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'feature:list';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List all ongoing features.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(FeatureRepository $featureRepository)
    {
        $features = $featureRepository->all();
        $currentFeature = Feature::current();

        $this->table(
            ['', 'ID', 'Name', 'Description', 'Type', 'Site'],
            $features->map(function(Feature $feature) use ($currentFeature) {
                return [
                    ($currentFeature !== null && $currentFeature->is($feature) ? '*' : ''),
                    $feature->getId(),
                    $feature->getName(),
                    $feature->getDescription(),
                    $feature->getType(),
                    $feature->getSite()->getName()
                ];
            })
        );
    }
}
