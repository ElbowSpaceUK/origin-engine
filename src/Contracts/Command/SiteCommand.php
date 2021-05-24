<?php

namespace OriginEngine\Contracts\Command;

use OriginEngine\Contracts\Site\SiteRepository;
use OriginEngine\Contracts\Site\SiteResolver;
use OriginEngine\Feature\Feature;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Site\Site;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

/**
 * A command that runs on a single site
 */
class SiteCommand extends Command
{

    private Site $site;

    private SiteRepository $siteRepository;

    private SiteResolver $siteResolver;
    private \Illuminate\Support\Collection $allSites;

    static bool $confirmedSite = false;

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
    protected function getSite(string $message = 'Which site would you like to perform the action against?', ?array $sites = null): Site
    {
        if(isset($this->site)) {
            return $this->site;
        }

        if(empty($sites) && !$this->sitesAreAvailable()) {
            throw new \Exception('No sites are available');
        }

        if($sites == null) {
            $sites = $this->getAvailableSites();
        }


        // Get the site from the default site
        if($this->getSiteResolver()->hasSite() && (
                static::$confirmedSite ||
                IO::confirm(sprintf('This will run on site \'%s\', is this correct?', $this->getSiteResolver()->getSite()->getName()), true)
            )
        ) {
            $this->site = $this->getSiteResolver()->getSite();
            static::$confirmedSite = true;
            return $this->site;
        }

        $siteId = $this->convertSiteTextIntoId(
            $this->getOrAskForOption(
                'site',
                fn() => $this->choice(
                    $message,
                    collect($sites)->mapWithKeys(fn(Site $site) => [sprintf('site-%u', $site->getId()) => $site->getName()])->toArray()
                ),
                fn($value) => $value && collect($sites)->map(fn($site) => $site->getId())->contains($this->convertSiteTextIntoId($value))
            )
        );

        $this->site = $this->getSiteRepository()->getById($siteId);

        return $this->site;
    }

    private function sitesAreAvailable(): bool
    {
        return $this->getAvailableSites()->count() > 0;
    }

    private function getSiteRepository(): SiteRepository
    {
        if(!isset($this->siteRepository)) {
            $this->siteRepository = app(SiteRepository::class);
        }
        return $this->siteRepository;
    }

    private function getAvailableSites(): Collection
    {
        if(!isset($this->allSites)) {
            $this->allSites = $this->getSiteRepository()->all()->filter();
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

    private function convertSiteTextIntoId(string $value): int
    {
        if(Str::startsWith($value, 'site-')) {
            return (int) Str::substr($value, 5);
        }
        return (int) $value;

    }

}
