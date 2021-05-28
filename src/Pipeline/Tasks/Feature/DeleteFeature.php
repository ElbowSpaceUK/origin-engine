<?php

namespace OriginEngine\Pipeline\Tasks\Feature;

use Illuminate\Support\Collection;
use OriginEngine\Contracts\Feature\FeatureRepository;
use OriginEngine\Contracts\Feature\FeatureResolver;
use OriginEngine\Feature\Feature;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Task;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Pipeline\TaskResponse;

class DeleteFeature extends Task
{

    public function __construct(Feature $feature)
    {
        parent::__construct([
            'feature' => $feature
        ]);
    }

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        /** @var Feature $feature */
        $feature = $config->get('feature');

        $this->writeInfo(sprintf('Deleting feature %s', $feature->getId()));

        $this->export('feature-id', $feature->getId());

        app(FeatureRepository::class)->delete(
            $config->get('feature')->getId()
        );

        return $this->succeeded();
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        app(FeatureRepository::class)->restore($output->get('feature-id'));
    }

    protected function upName(Collection $config): string
    {
        return 'Delete feature';
    }

    protected function downName(Collection $config): string
    {
        return 'Delete feature';
    }
}
