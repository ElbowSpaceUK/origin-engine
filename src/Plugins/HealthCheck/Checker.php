<?php

namespace OriginEngine\Plugins\HealthCheck;

use OriginEngine\Site\Site;

abstract class Checker
{
    protected bool $isQuickCheck = true;

    /**
     * Determine if the site passes the check
     *
     * @param Site $site The site to check
     * @return CheckerStatus The status of the check
     */
    abstract public function check(Site $site): CheckerStatus;

    /**
     * Fix the site so it passes the checks
     *
     * @param Site $site The site to check
     */
    abstract public function fix(Site $site): void;

    /**
     * Check and, if necessary fix, the site so it passes the checks
     *
     * @param Site $site The site to check
     */
    public function checkAndFix(Site $site)
    {
        if($this->check($site)->getStatus() === false) {
            $this->fix($site);
        }
    }

    /**
     * The name of the checker.
     *
     * Should follow 'checking', for example
     * - 'the site files are in the right placee'
     * - 'the site dependencies are installed'
     *
     * @return string
     */
    abstract public function checking(): string;

    protected function failedDueTo(string $message = 'the check failing'): CheckerStatus
    {
        return CheckerStatus::failedDueTo($message);
    }

    protected function succeededDueTo(string $message = 'the check passing'): CheckerStatus
    {
        return CheckerStatus::succeededDueTo($message);
    }

    public function isQuickCheck(): bool
    {
        return $this->isQuickCheck;
    }
}
