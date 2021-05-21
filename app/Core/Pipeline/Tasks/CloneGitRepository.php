<?php

namespace App\Core\Pipeline\Tasks;

use App\Core\Contracts\Pipeline\Task;
use App\Core\Helpers\Storage\Filesystem;
use App\Core\Pipeline\ProvisionedTask;
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

    public function up(\App\Core\Helpers\WorkingDirectory\WorkingDirectory $workingDirectory): void
    {
        GitRepository::cloneRepository(
            $this->config->get('repository'),
            $workingDirectory->path(),
            [
                '--branch' => $this->config->get('branch')
            ]
        );
    }

    public function down(\App\Core\Helpers\WorkingDirectory\WorkingDirectory $workingDirectory): void
    {
        Filesystem::create()
            ->remove($workingDirectory->path());
    }
}
