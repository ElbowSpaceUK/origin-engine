<?php

namespace OriginEngine\Pipeline\Tasks\Site;

use Illuminate\Support\Collection;
use OriginEngine\Contracts\Site\SiteRepository;
use OriginEngine\Site\Site;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Task;
use OriginEngine\Pipeline\TaskResponse;

class CreateSite extends Task
{

    public function __construct(string $name, ?string $description, string $blueprintAlias)
    {
        parent::__construct([
            'name' => $name,
            'description' => $description,
            'blueprint-alias' => $blueprintAlias
        ]);
    }

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        $site = app(SiteRepository::class)->create(
            $workingDirectory->getPathBasename(),
            $config->get('name'),
            $config->get('description'),
            $config->get('blueprint-alias')
        );

        $this->writeSuccess(sprintf('Created site %u', $site->getId()));

        $this->export('site', $site);

        return $this->succeeded();
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        app(SiteRepository::class)->delete($output->get('site')->getId());
    }

    protected function upName(Collection $config): string
    {
        return 'Create site';
    }

    protected function downName(Collection $config): string
    {
        return 'Create site';
    }
}
