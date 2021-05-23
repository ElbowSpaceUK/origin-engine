<?php

namespace OriginEngine\Pipeline\Tasks;

use Illuminate\Support\Collection;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;
use OriginEngine\Pipeline\Task;
use OriginEngine\Helpers\Storage\Filesystem;
use Cz\Git\GitRepository;
use OriginEngine\Pipeline\TaskResponse;

class CloneGitRepository extends Task
{

    public function __construct(string $repository, string $branch = 'develop')
    {
        parent::__construct([
            'repository' => $repository,
            'branch' => $branch
        ]);
    }

    protected function execute(WorkingDirectory $workingDirectory, Collection $config): TaskResponse
    {
        GitRepository::cloneRepository(
            $config->get('repository'),
            $workingDirectory->path(),
            [
                '--branch' => $config->get('branch')
            ]
        );
        $this->writeSuccess(sprintf('Installed repository %s', $config->get('repository')));

        return $this->succeeded();
    }

    protected function undo(WorkingDirectory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        Filesystem::create()
            ->remove($workingDirectory->path());
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
