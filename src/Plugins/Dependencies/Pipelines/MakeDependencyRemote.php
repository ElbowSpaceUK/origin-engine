<?php

namespace OriginEngine\Plugins\Dependencies\Pipelines;

use OriginEngine\Pipeline\Pipeline;
use OriginEngine\Pipeline\Tasks\Composer\ComposerUpdate;
use OriginEngine\Pipeline\Tasks\DeleteFiles;
use OriginEngine\Plugins\Dependencies\LocalPackage;
use OriginEngine\Plugins\Dependencies\Tasks\ComposerRemoveLocalSymlink;
use OriginEngine\Plugins\Dependencies\Tasks\ComposerRemovePackageLocally;
use OriginEngine\Plugins\Dependencies\Tasks\DeleteDependencyFromVendor;
use OriginEngine\Plugins\Dependencies\Tasks\MarkDependencyAsRemote;

class MakeDependencyRemote extends Pipeline
{

    private LocalPackage $localPackage;

    public function __construct(LocalPackage $localPackage)
    {
        $this->localPackage = $localPackage;
    }

    protected function tasks(): array
    {
        $path = $this->localPackage->getPathRelativeToRoot();

        return [
            'remove-local-symlink' => new ComposerRemoveLocalSymlink(sprintf('./%s', $path)),
            'modify-version-constraints' => new ComposerRemovePackageLocally($this->localPackage),
            'remove-local-repository' => new DeleteFiles($path),
            'mark-dependency-as-remote' => new MarkDependencyAsRemote($this->localPackage),
            'clear-stale-dependencies' => new DeleteDependencyFromVendor($this->localPackage->getName()),
            'update-composer' => new ComposerUpdate($this->localPackage->getParentFeature()->getSite()->getBlueprint()->getPhpVersion())
        ];
    }
}
