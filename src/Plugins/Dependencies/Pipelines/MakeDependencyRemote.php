<?php

namespace OriginEngine\Plugins\Dependencies\Pipelines;

use OriginEngine\Helpers\Composer\ComposerRunner;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Pipeline\Pipeline;
use OriginEngine\Pipeline\TaskResponse;
use OriginEngine\Pipeline\Tasks\Composer\ComposerUpdate;
use OriginEngine\Pipeline\Tasks\DeleteFiles;
use OriginEngine\Pipeline\Tasks\Git\CheckoutBranch;
use OriginEngine\Pipeline\Tasks\Git\CloneGitRepository;
use OriginEngine\Pipeline\Tasks\Utils\Closure;
use OriginEngine\Plugins\Dependencies\LocalPackage;
use OriginEngine\Plugins\Dependencies\Tasks\ComposerAddLocalSymlink;
use OriginEngine\Plugins\Dependencies\Tasks\ComposerRemoveLocalSymlink;
use OriginEngine\Plugins\Dependencies\Tasks\ComposerRemovePackageLocally;
use OriginEngine\Plugins\Dependencies\Tasks\ComposerRequirePackageLocally;
use OriginEngine\Plugins\Dependencies\Tasks\DeleteDependencyFromVendor;

class MakeDependencyRemote extends Pipeline
{

    private LocalPackage $localPackage;

    public function __construct(LocalPackage $localPackage)
    {
        $this->localPackage = $localPackage;
    }

    protected function tasks(): array
    {
        $path = sprintf('repos/%s', $this->localPackage->getName());

        return [
            'remove-local-symlink' => new ComposerRemoveLocalSymlink(sprintf('./%s', $path)),
            'modify-version-constraints' => new ComposerRemovePackageLocally($this->localPackage),
            'remove-local-repository' => new DeleteFiles($path),
            'clear-stale-dependencies' => new DeleteDependencyFromVendor($this->localPackage->getName()),
            'update-composer' => new ComposerUpdate()
        ];
    }
}
