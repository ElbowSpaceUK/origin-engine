<?php

namespace OriginEngine\Plugins\HealthCheck\Checkers;

use OriginEngine\Contracts\Site\SiteRepository;
use OriginEngine\Plugins\HealthCheck\Checker;
use OriginEngine\Plugins\HealthCheck\CheckerStatus;
use OriginEngine\Site\Site;

class SiteFileIntegrityChecker extends Checker
{

    protected bool $isQuickCheck = false;

    /**
     * Determine if the site passes the check
     *
     * @param Site $site The site to check
     * @return CheckerStatus The status of the check
     */
    public function check(Site $site): CheckerStatus
    {
        if($site->getStatus() === Site::STATUS_MISSING) {
            return $this->failedDueTo('files missing from project directory');
        }
        return $this->succeededDueTo('files found in correct place');
    }

    /**
     * Fix a broken site so it passes the check
     *
     * @param Site $site The site to check
     */
    public function fix(Site $site): void
    {
        app(SiteRepository::class)->delete($site->getId());
    }

    /**
     * @inheritDoc
     */
    public function checking(): string
    {
        return 'the site file integrity';
    }
}
