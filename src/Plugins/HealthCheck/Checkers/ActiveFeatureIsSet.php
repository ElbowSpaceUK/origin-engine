<?php

namespace OriginEngine\Plugins\HealthCheck\Checkers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Artisan;
use OriginEngine\Contracts\Feature\FeatureRepository;
use OriginEngine\Contracts\Feature\FeatureResolver;
use OriginEngine\Contracts\Site\SiteRepository;
use OriginEngine\Plugins\HealthCheck\Checker;
use OriginEngine\Plugins\HealthCheck\CheckerStatus;
use OriginEngine\Site\Site;

/**
 * Changes the feature resolver to match the currently checked out branch.
 */
class ActiveFeatureIsSet extends Checker
{

    /**
     * Determine if the site passes the check
     *
     * @param Site $site The site to check
     * @return CheckerStatus The status of the check
     */
    public function check(Site $site): CheckerStatus
    {

        // If a feature is not checked out, and the branch = site default branch
            // All good. No feature checked out
        // If a feature is checked out, branch equal checked out feature
            // All good. Right feature checked out

        // If the branch does not equal site default branch
            // Need to check out the right feature. If doesn't exist, create.
        // If a feature is checked out, and the branch = site default branch
            // Clear the feature




        // Change the used feature to match what it actually is, if that feature is in the db.
        // If it isn't (by branch), will either say they need to create a feature with the branch x or reset the site.
        // Can call those commands for them, ask what they want or nothing.

        $currentBranch = 'develop';
        $hasFeature = app(FeatureRepository::class)->hasFeature($site);

        // If no feature is checked out in the file system...
        if($currentBranch === $site->getBlueprint()->getDefaultBranch()) {
            if (!$hasFeature) {
                return $this->succeededDueTo('no feature being checked out');
            }
            return $this->failedDueTo('a feature being checked out when the feature is not in use');
        }

        // If a feature is checked out in the filesystem

        // If the saved feature in the db is the one checked out in the filesystem
        if ($hasFeature && $currentBranch === app(FeatureResolver::class)->getFeature($site)->getBranch()) {
            return $this->succeededDueTo('correct feature being checked out');
        }

        // If the feature is not checked out in the db, but is in the filesystem
        if(!$hasFeature) {
            try {
                $checkedOutFeature = app(FeatureRepository::class)->getByBranchAndSite($site, $currentBranch);
            } catch (ModelNotFoundException $e) {
                return $this->failedDueTo('the feature checked out in the filesystem does not exist.');

                Artisan::call('feature:new', [
                    '--branch' => $currentBranch,
                    '--site' => $site->getId()
                ]);
                $checkedOutFeature = app(FeatureRepository::class)->getByBranchAndSite($site, $currentBranch);
                app(FeatureResolver::class)->setFeature($checkedOutFeature);
                // Handle the case of the feature does not exist, so must create it or reset the site. Unless the branch is the same as the default branch?
            }
            // Get the feature for the branch $currentBranch, and set the current feature.
        }

        return $this->failedDueTo('origin thinking the wrong feature was checked out.');
//          app(FeatureResolver::class)->clearFeature($site);
        // If current branch the same as default branch, and feature not set, fine.

        // If current branch the same as default branch, but feature set, clear the feature.

        try {
            $checkedOutFeature = app(FeatureRepository::class)->getByBranchAndSite($site, $currentBranch);
        } catch (ModelNotFoundException $e) {
            // Handle the case of the feature does not exist, so must create it or reset the site. Unless the branch is the same as the default branch?
        }

        $currentFeature = app(FeatureResolver::class)->getFeature();

        // If a feature is the one in the db
        if (!app(FeatureResolver::class)->hasFeature($site)) {
            return $this->failedDueTo('no feature being checked out in origins records');
        }
        // If the feature is the same as the checked out feature
        if (app(FeatureResolver::class)->getFeature($site)->getId() === $currentFeature->getId()) {
            return $this->succeededDueTo('the correct feature being checked out');
        }
        return $this->failedDueTo('the checked out feature was different to origins records.');
    }

    /**
     * Fix a broken site so it passes the check
     *
     * @param Site $site The site to check
     */
    public function fix(Site $site): void
    {
        $currentBranch = 'develop';
        $hasFeature = app(FeatureRepository::class)->hasFeature($site);

        if ($hasFeature && $currentBranch === $site->getBlueprint()->getDefaultBranch()) {
            app(FeatureResolver::class)->clearFeature($site);
        }

        if($currentBranch !== $site->getBlueprint()->getDefaultBranch()) {
            // If the saved feature in the db is the one checked out in the filesystem
            if ($hasFeature && $currentBranch === app(FeatureResolver::class)->getFeature($site)->getBranch()) {
//                return $this->succeededDueTo('correct feature being checked out');
            }

            if($hasFeature)
            try {
                $checkedOutFeature = app(FeatureRepository::class)->getByBranchAndSite($site, $currentBranch);
            } catch (ModelNotFoundException $e) {
                Artisan::call('feature:new', [
                    '--branch' => $currentBranch,
                    '--site' => $site->getId()
                ]);
                $checkedOutFeature = app(FeatureRepository::class)->getByBranchAndSite($site, $currentBranch);
                app(FeatureResolver::class)->setFeature($checkedOutFeature);
                // Handle the case of the feature does not exist, so must create it or reset the site. Unless the branch is the same as the default branch?
            }

            if(!$hasFeature) {

                // Get the feature for the branch $currentBranch, and set the current feature.
            }

        }


        // If the feature is not checked out in the db, but is in the filesystem
        // Check out the right feature.









        $currentBranch = 'develop';

        if ($currentBranch === $site->getBlueprint()->getDefaultBranch() && app(FeatureResolver::class)->hasFeature($site)) {
            app(FeatureResolver::class)->clearFeature($site);
        }

        try {
            $checkedOutFeature = app(FeatureRepository::class)->getByBranchAndSite($site, $currentBranch);
        } catch (ModelNotFoundException $e) {
            // Handle the case of the feature does not exist, so must create it or reset the site. Unless the branch is the same as the default branch?
        }

        $currentFeature = app(FeatureResolver::class)->getFeature();

        // If a feature is the one in the db
        if (!app(FeatureResolver::class)->hasFeature($site)) {
            return $this->failedDueTo('no feature being checked out in origins records');
        }
        // If the feature is the same as the checked out feature
        if (app(FeatureResolver::class)->getFeature($site)->getId() === $currentFeature->getId()) {
            return $this->succeededDueTo('the correct feature being checked out');
        }
        return $this->failedDueTo('the checked out feature was different to origins records.');
    }

    /**
     * @inheritDoc
     */
    public function checking(): string
    {
        return 'the active feature needs updating';
    }
}
