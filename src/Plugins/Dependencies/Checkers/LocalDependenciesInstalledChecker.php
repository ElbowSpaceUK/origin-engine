<?php

namespace OriginEngine\Plugins\Dependencies\Checkers;

use Illuminate\Support\Facades\Artisan;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Plugins\Dependencies\Contracts\LocalPackageRepository;
use OriginEngine\Plugins\Dependencies\LocalPackageDatabaseRepository;
use OriginEngine\Plugins\HealthCheck\Checker;
use OriginEngine\Plugins\HealthCheck\CheckerStatus;
use OriginEngine\Site\Site;

/**
 * Changes the feature resolver to match the currently checked out branch.
 */
class LocalDependenciesInstalledChecker extends Checker
{

    /**
     * Determine if the site passes the check
     *
     * @param Site $site The site to check
     * @return CheckerStatus The status of the check
     */
    public function check(Site $site): CheckerStatus
    {
        if(!$site->hasCurrentFeature()) {
            return $this->succeededDueTo('no feature being checked out');
        }

        foreach(app(LocalPackageRepository::class)->getAllThroughFeature($site->getCurrentFeature()->getId()) as $dependency) {
            if(!$dependency->isLocal()) {
                continue;
            }
            $dependencyPath = Filesystem::append(
                Filesystem::project(),
                $site->getDirectoryPath(),
                $dependency->getPathRelativeToRoot()
            );
            if(!Filesystem::create()->exists($dependencyPath)) {
                return $this->failedDueTo('dependencies not being checked out');
            }
        }

        return $this->succeededDueTo('all dependencies checked out');
    }

    /**
     * Fix a broken site so it passes the check
     *
     * @param Site $site The site to check
     */
    public function fix(Site $site): void
    {
        if($site->hasCurrentFeature()) {
            foreach(app(LocalPackageRepository::class)->getAllThroughFeature($site->getCurrentFeature()->getId()) as $dependency) {
                if(!$dependency->isLocal()) {
                    continue;
                }
                $dependencyPath = Filesystem::append(
                    Filesystem::project(),
                    $site->getDirectoryPath(),
                    $dependency->getPathRelativeToRoot()
                );
                if(!Filesystem::create()->exists($dependencyPath)) {
                    Artisan::call('dep:local', [
                        '--site' => $site->getId(),
                        '--package' => $dependency->getName()
                    ]);
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function checking(): string
    {
        return 'the local dependencies are in sync';
    }
}
