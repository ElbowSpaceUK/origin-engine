<?php

namespace App\Core\Contracts\Command;

use App\Core\Contracts\Site\SiteRepository;
use App\Core\Contracts\Site\SiteResolver;
use App\Core\Helpers\IO\IO;
use App\Core\Site\Site;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

/**
 * A command that runs on a single site
 */
class SiteCommand extends Command#
{

    private Site $site;

    private SiteRepository $siteRepository;

    private SiteResolver $siteResolver;
    private \Illuminate\Database\Eloquent\Collection $allSites;

    protected function configure()
    {
        parent::configure();
        $this->addOption('site', 'S', InputOption::VALUE_OPTIONAL, 'The ID of the site', null);

    }

    /**
     * Get the site the user wants to use
     *
     * @param string $message The message to show to the user if they're asked
     * @param \Closure|null $siteFilter A callback that takes a Site instance and returns true or false as to whether the user can use it.
     * @param bool $ignoreDefault True will not use the default site and instead always prompt the user for the site.
     *
     * @return Site
     * @throws \Exception If no sites are available or the chosen site could not be found
     */
    protected function getSite(string $message = 'Which site would you like to perform the action against?',
                            \Closure $siteFilter = null,
                            bool $ignoreDefault = false): Site
    {
        if(isset($this->site)) {
            return $this->site;
        }

        if(!$this->sitesAreAvailable($siteFilter)) {
            throw new \Exception('No sites are available');
        }

        // Get the site from the default site
        if($ignoreDefault === false && $this->getSiteResolver()->hasSite()) {
            return $this->cacheSite($this->getSiteResolver()->getSite());
        }

        $siteId = $this->option('site') ?? $this->promptUserForSite($message, $siteFilter);

        $site = $this->getSiteRepository()->getById($siteId);

        $this->site = $site;
        return $this->site;
    }

    private function sitesAreAvailable(?\Closure $siteFilter): bool
    {
        return $this->getAvailableSites($siteFilter)->count() > 0;
    }

    private function getSiteRepository(): SiteRepository
    {
        if(!isset($this->siteRepository)) {
            $this->siteRepository = app(SiteRepository::class);
        }
        return $this->siteRepository;
    }

    private function getAvailableSites(?\Closure $siteFilter = null): Collection
    {
        if(!isset($this->allSites)) {
            $this->allSites = $this->getSiteRepository()->all()->filter($siteFilter);
        }

        return $this->allSites;
    }

    private function getSiteResolver(): SiteResolver
    {
        if(!isset($this->siteResolver)) {
            $this->siteResolver = app(SiteResolver::class);
        }
        return $this->siteResolver;
    }

    private function cacheSite(Site $site): Site
    {
        $this->site = $site;
        return $this->site;
    }

    private function convertSiteTextIntoId(string $value): int
    {
        if(Str::startsWith($value, 'site-')) {
            return (int) Str::substr($value, 5);
        }
        return (int) $value;

    }

    private function promptUserForSite(string $message, ?\Closure $siteFilter): int
    {
        $prefixedSiteId = $this->choice(
            $message,
            $this->getAvailableSites($siteFilter)->mapWithKeys(fn(Site $site) => [sprintf('site-%u', $site->getId()) => $site->getName()])->toArray()
        );

        if(!$prefixedSiteId || !$this->getAvailableSites($siteFilter)->map(fn($site) => $site->getId())->contains($this->convertSiteTextIntoId($prefixedSiteId))) {
            IO::error(sprintf('[%s] is not a valid site', $prefixedSiteId));
            return $this->promptUserForSite($message, $siteFilter);
        }

        return $this->convertSiteTextIntoId($prefixedSiteId);
    }

}
