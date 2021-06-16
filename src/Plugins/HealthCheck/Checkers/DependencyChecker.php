<?php

namespace OriginEngine\Plugins\HealthCheck\Checkers;

use Cz\Git\GitRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Artisan;
use OriginEngine\Contracts\Feature\FeatureRepository;
use OriginEngine\Contracts\Feature\FeatureResolver;
use OriginEngine\Contracts\Site\SiteRepository;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Plugins\HealthCheck\Checker;
use OriginEngine\Plugins\HealthCheck\CheckerStatus;
use OriginEngine\Site\Site;

/**
 * Changes the feature resolver to match the currently checked out branch.
 */
class DependencyChecker extends Checker
{

    /**
     * Determine if the site passes the check
     *
     * @param Site $site The site to check
     * @return CheckerStatus The status of the check
     */
    public function check(Site $site): CheckerStatus
    {
        // For each of the dependencies of the site
            // If the folder does not exist, fail due to missing local dep (or install)
            // If it does, succeed
        // For each dependency file, discounting ones that are dependencies already
            // Ask if it should be added and add it from composer
    }

    /**
     * Fix a broken site so it passes the check
     *
     * @param Site $site The site to check
     */
    public function fix(Site $site): void
    {
        // For each of the dependencies of the site
        // If the folder does not exist, fail due to missing local dep (or install)
        // If it does, succeed
        // For each dependency file, discounting ones that are dependencies already
        // Ask if it should be added and add it from composer
    }

    /**
     * @inheritDoc
     */
    public function checking(): string
    {
        return 'the local dependencies are in sync';
    }
}
