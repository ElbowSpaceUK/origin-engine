<?php

namespace OriginEngine\Plugins\Dependencies\Commands\Pipelines;

use OriginEngine\Helpers\Composer\ComposerRunner;
use OriginEngine\Pipeline\Pipeline;
use OriginEngine\Pipeline\TaskResponse;
use OriginEngine\Pipeline\Tasks\Composer\ComposerUpdate;
use OriginEngine\Pipeline\Tasks\Git\CheckoutBranch;
use OriginEngine\Pipeline\Tasks\Git\CloneGitRepository;
use OriginEngine\Pipeline\Tasks\Utils\Closure;
use OriginEngine\Plugins\Dependencies\LocalPackage;
use OriginEngine\Plugins\Dependencies\Tasks\ComposerAddLocalSymlink;
use OriginEngine\Plugins\Dependencies\Tasks\ComposerRequirePackageLocally;
use OriginEngine\Plugins\Dependencies\Tasks\DeleteDependencyFromVendor;

class MakeExistingDependencyLocal extends Pipeline
{


    private LocalPackage $localPackage;

    public function __construct(LocalPackage $localPackage)
    {
        $this->localPackage = $localPackage;
    }

    protected function tasks(): array
    {
        $path = sprintf('repos/%s', $this->localPackage->getName());

        $checkoutBranch = new CheckoutBranch($this->localPackage->getFeature()->getBranch(), true);
        $checkoutBranch->inRelativeDirectory($path);

        return [
            'clear-stale-dependencies' => new DeleteDependencyFromVendor($this->localPackage->getName()),
            'clone-repository' => new CloneGitRepository(
                $this->localPackage->getUrl(),
                null,
                $path
            ),
            'checkout-branch' => $checkoutBranch,
            'modify-composer-json' => new ComposerRequirePackageLocally($this->localPackage->getName(), $this->localPackage->getFeature()->getBranch()),
            'add-local-symlink' => new ComposerAddLocalSymlink(sprintf('./%s', $path)),
            'update-composer' => new ComposerUpdate()
        ];
    }
}
