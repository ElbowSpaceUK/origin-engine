<?php

namespace OriginEngine\Pipeline\Old\Tasks;

use OriginEngine\Contracts\Pipeline\Task;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Pipeline\Old\ProvisionedTask;
use Cz\Git\GitRepository;

class CloneGitRepository extends Task
{

    public static function provision(string $repository, string $branch = 'develop'): ProvisionedTask
    {
        return ProvisionedTask::provision(self::class)
            ->dependencies([
                'repository' => $repository,
                'branch' => $branch
            ]);
    }

    public function up(\OriginEngine\Helpers\WorkingDirectory\WorkingDirectory $workingDirectory): void
    {
        GitRepository::cloneRepository(
            $this->config->get('repository'),
            $workingDirectory->path(),
            [
                '--branch' => $this->config->get('branch')
            ]
        );
    }

    public function down(\OriginEngine\Helpers\WorkingDirectory\WorkingDirectory $workingDirectory): void
    {
        Filesystem::create()
            ->remove($workingDirectory->path());
    }
}
