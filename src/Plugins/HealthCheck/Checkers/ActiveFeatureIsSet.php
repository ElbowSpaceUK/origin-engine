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
        $currentBranch = (new GitRepository(Filesystem::project($site->getDirectoryPath())))->getCurrentBranchName();

        $hasFeature = app(FeatureResolver::class)->hasFeature($site);

        // If the site should not have a feature set as active
        if($currentBranch === $site->getBlueprint()->getDefaultBranch()) {
            if(app(FeatureResolver::class)->hasFeature($site)) {
                return $this->failedDueTo('A feature being used but not checked out');
            }
            return $this->succeededDueTo('No feature checked out or being used');
        } else {
            try {
                $feature = app(FeatureRepository::class)->getByBranchAndSite($site, $currentBranch);
            } catch (ModelNotFoundException $e) {
                return $this->failedDueTo('A non-existent feature being checked out');
            }
            if(!$hasFeature) {
                return $this->failedDueTo('A feature being checked out but not used');
            } else {
                if(app(FeatureResolver::class)->getFeature($site)->getBranch() === $currentBranch) {
                    return $this->succeededDueTo('The same feature being checked out and used');
                } else {
                    return $this->failedDueTo('A different feature being checked out than used');
                }
            }
        }

    }

    /**
     * Fix a broken site so it passes the check
     *
     * @param Site $site The site to check
     */
    public function fix(Site $site): void
    {
        $currentBranch = (new GitRepository(Filesystem::project($site->getDirectoryPath())))->getCurrentBranchName();
        /** @var FeatureResolver $featureResolver */
        $featureResolver = app(FeatureResolver::class);
        $hasFeature = $featureResolver->hasFeature($site);
        // If the site should not have a feature set as active
        if($currentBranch === $site->getBlueprint()->getDefaultBranch()) {
            if($hasFeature) {
                $featureResolver->clearFeature($site);
            }
        } else {
            try {
                $checkedOutFeature = app(FeatureRepository::class)->getByBranchAndSite($site, $currentBranch);
                if($hasFeature) {
                    if($currentBranch !== $featureResolver->getFeature($site)->getBranch()) {
                        $featureResolver->clearFeature($site);
                        $featureResolver->setFeature($checkedOutFeature);
                    }
                } else {
                    $featureResolver->setFeature($checkedOutFeature);
                }
            } catch (ModelNotFoundException $e) {
                $type = IO::choice(
                    sprintf(
                        'The checked out feature does not currently exist in Origin. Would you like to set the [%s] branch up as a feature, or reset the site to remove the branch?',
                        $currentBranch
                    ),
                    [
                        'create' => sprintf('Create a new feature using the [%s] branch', $currentBranch),
                        'reset' => sprintf('Reset the site to [%s]', $site->getBlueprint()->getDefaultBranch()),
                        'cancel' => 'Do not fix this now'
                    ]
                );
                switch($type) {
                    case 'create':
                        Artisan::call('feature:new', [
                            '--branch' => $currentBranch,
                            '--site' => $site->getId()
                        ]);
                        $checkedOutFeature = app(FeatureRepository::class)->getByBranchAndSite($site, $currentBranch);
                        break;
                    case 'reset':
                        Artisan::call('site:reset', [
                            '--site' => $site->getId()
                        ]);
                    default:
                        break;
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function checking(): string
    {
        return 'the active feature needs updating';
    }
}
