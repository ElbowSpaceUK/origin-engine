<?php

namespace OriginEngine\Command;

use OriginEngine\Contracts\Feature\FeatureRepository;
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

    private Site $cachedSite;

    private Feature $cachedFeature;

    static bool $confirmedSite = false;

    static bool $confirmedFeature = false;

    protected function configure()
    {
        parent::configure();
        $this->addOption('site', 'S', InputOption::VALUE_OPTIONAL, 'The ID of the site', null);
        $this->addOption('feature', 'F', InputOption::VALUE_OPTIONAL, 'The ID of the feature', null);
    }

    /**
     * Get the site the user wants to use
     *
     * @param string $message The message to show to the user if they're asked
     * @param array|null $sites An array of allowed sites
     *
     * @return Site
     * @throws \Exception If no sites are available or the chosen site could not be found
     */
    protected function getSite(string $message = 'Which site would you like to use?', ?array $sites = null): Site
    {
        if(isset($this->cachedSite)) {
            return $this->cachedSite;
        }

        // Collect together the allowed sites
        if($sites === null) {
            $sites = app(SiteRepository::class)->all();
        }
        $sites = collect($sites);
        if(empty($sites)) {
            throw new \Exception('No sites are available');
        }

        // Get the site from the default site if set
        if(app(SiteResolver::class)->hasSite() && (
                static::$confirmedSite ||
                IO::confirm(sprintf('This will run on site \'%s\', is this correct?', app(SiteResolver::class)->getSite()->getName()), true)
            )
        ) {
            $this->cachedSite = app(SiteResolver::class)->getSite();
            static::$confirmedSite = true;
            return $this->cachedSite;
        }

        // Ask the user which site to use
        $siteId = $this->convertChoiceTextIntoId(
            $this->getOrAskForOption(
                'site',
                fn() => $this->choice(
                    $message,
                    $sites->mapWithKeys(fn(Site $site) => [sprintf('site-%u', $site->getId()) => $site->getName()])->toArray()
                ),
                fn($value) => $value && $sites->map(fn($site) => $site->getId())->contains($this->convertChoiceTextIntoId($value, 'site-'))
            ),
            'site-'
        );

        $this->cachedSite = app(SiteRepository::class)->getById($siteId);

        return $this->cachedSite;
    }

    /**
     * Get the feature the user wants to use
     *
     * @param string $message The message to show to the user if they're asked
     * @param array|null $features The features that are allowed
     *
     * @return Feature
     * @throws \Exception If no features are available or the chosen feature could not be found
     */
    protected function getFeature(string $message = 'Which feature would you like to use?', ?array $features = null): Feature
    {
        if(isset($this->cachedFeature)) {
            return $this->cachedFeature;
        }

        // Collect together the allowed features
        if($features === null) {
            $features = app(FeatureRepository::class)->all();
        }
        $features = collect($features);
        if(empty($features)) {
            throw new \Exception('No features are available');
        }

        // Get the site from the default site if set
        if(
            app(SiteResolver::class)->hasSite() &&
            app(SiteResolver::class)->getSite()->hasCurrentFeature() &&
            (
                static::$confirmedFeature ||
                IO::confirm(sprintf('This will run on feature \'%s\', is this correct?', app(SiteResolver::class)->getSite()->getCurrentFeature()->getName()), true)
            )
        ) {
            $this->cachedFeature = app(SiteResolver::class)->getSite()->getCurrentFeature();
            static::$confirmedFeature = true;
            return $this->cachedFeature;
        }

        // Ask the user which feature/dependency to use
        $featureId = $this->convertChoiceTextIntoId(
            $this->getOrAskForOption(
                'feature',
                fn() => $this->choice(
                    $message,
                    $features->mapWithKeys(function (Feature $feature) {
                        $featureNamePrefix = $feature->isDependency() ? 'Dependency' : 'Main repo';
                        return [sprintf('feature-%u', $feature->getId()) => sprintf('%s - %s', $featureNamePrefix, $feature->getName())];
                    })->toArray()
                ),
                fn($value) => $value && $features->map(fn($feature) => $feature->getId())->contains($this->convertChoiceTextIntoId($value, 'feature-'))
            ),
            'feature-'
        );

        $this->cachedFeature = app(FeatureRepository::class)->getById($featureId);

        return $this->cachedFeature;
    }

    public function getMainFeature(string $message = 'Which feature would you like to use?'): Feature
    {
        $features = app(FeatureRepository::class)->all()->filter(
            fn(Feature $feature) => !$feature->isDependency()
        )->all();

        return $this->getFeature($message, $features);
    }

    private function convertChoiceTextIntoId(string $value, string $prefix): int
    {
        if(Str::startsWith($value, $prefix)) {
            return (int) Str::substr($value, strlen($prefix));
        }
        return (int) $value;

    }

}
