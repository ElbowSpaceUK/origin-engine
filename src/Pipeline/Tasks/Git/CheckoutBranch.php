<?php

namespace OriginEngine\Pipeline\Tasks\Git;

use Cz\Git\GitException;
use Illuminate\Support\Collection;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Task;
use OriginEngine\Helpers\Storage\Filesystem;
use Cz\Git\GitRepository;
use OriginEngine\Pipeline\TaskResponse;

class CheckoutBranch extends Task
{

    public function __construct(string $branch, bool $createIfMissing = false)
    {
        parent::__construct([
            'branch' => $branch,
            'createIfMissing' => $createIfMissing
        ]);
    }

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        $branch = $config->get('branch');
        $git = new GitRepository($workingDirectory->path());

        $this->export('current-branch', $git->getCurrentBranchName());

        try {
            $this->writeDebug(sprintf('Attempting to checkout branch %s', $branch));
            $git->checkout($branch);
        } catch (GitException $e) {
            $this->writeError($e->getMessage());
            if($config->get('createIfMissing', false)) {
                $this->writeInfo(sprintf('Creating branch %s', $branch));
                $git->createBranch($branch, true);
            }
        }

        $this->writeSuccess(sprintf('Checked out branch %s', $branch));

        return $this->succeeded();
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        $branch = $output->get('current-branch');
        $git = new GitRepository($workingDirectory->path());

        try {
            $git->checkout($branch);
        } catch (GitException $e) {
            $git->createBranch($branch, true);
        }
    }

    protected function upName(Collection $config): string
    {
        return sprintf('Checking out %s', $config->get('branch'));
    }

    protected function downName(Collection $config): string
    {
        return sprintf('Reverting from %s', $config->get('branch'));
    }
}
