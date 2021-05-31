<?php

namespace OriginEngine\Pipeline\Tasks\Git;

use Illuminate\Support\Collection;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Task;
use OriginEngine\Helpers\Storage\Filesystem;
use Cz\Git\GitRepository;
use OriginEngine\Pipeline\TaskResponse;

class CloneGitRepository extends Task
{

    public function __construct(string $repository, ?string $branch = null, ?string $pathOverride = null)
    {
        parent::__construct([
            'repository' => $repository,
            'branch' => $branch,
            'pathOverride' => $pathOverride
        ]);
    }

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        GitRepository::cloneRepository(
            $config->get('repository'),
            $config->get('pathOverride') !== null ? Filesystem::append($workingDirectory->path(), $config->get('pathOverride')) : $workingDirectory->path(),
            array_merge(($config->get('branch') !== null ? ['--branch' => $config->get('branch')] : []))
        );
        $this->writeSuccess(sprintf('Cloned repository %s', $config->get('repository')));

        return $this->succeeded();
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        Filesystem::create()
            ->remove(
                $config->get('pathOverride') !== null ? Filesystem::append($workingDirectory->path(), $config->get('pathOverride')) : $workingDirectory->path()
            );
    }

    protected function upName(Collection $config): string
    {
        return sprintf('Installing %s', $config->get('repository'));
    }

    protected function downName(Collection $config): string
    {
        return sprintf('Installing %s', $config->get('repository'));
    }
}
