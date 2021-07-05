<?php

namespace OriginEngine\Update;

use Github\Client;
use Humbug\SelfUpdate\Strategy\GithubStrategy;
use Humbug\SelfUpdate\Updater;
use Humbug\SelfUpdate\VersionParser;
use LaravelZero\Framework\Components\Updater\Strategy\StrategyInterface;
use OriginEngine\Contracts\Helpers\Settings\SettingRepository;

class GithubPrivateReleaseStrategy extends GithubStrategy implements StrategyInterface
{

    /**
     * @var array
     */
    private $release;

    public function download(Updater $updater)
    {
        if(count($this->release['assets']) === 0) {
            throw new \Exception('No assets uploaded to release');
        }
        $assetId = $this->release['assets'][0]['id'];

        $settings = app(SettingRepository::class);
        if(!$settings->has('github-release-token')) {
            throw new \Exception('A github token must be set first');
        }

        $client = Client::createWithHttpClient(new \GuzzleHttp\Client());
        $client->authenticate($settings->get('github-release-token'), null, \Github\Client::AUTH_ACCESS_TOKEN);

        $githubUsername = config('updater.github.username');
        $githubRepository = config('updater.github.repository');

        $content = $client->repo()->releases()->assets()->show($githubUsername, $githubRepository, $assetId, true);

        file_put_contents($updater->getTempPharFile(), $content);
    }

    public function getCurrentRemoteVersion(Updater $updater)
    {
        $settings = app(SettingRepository::class);
        if(!$settings->has('github-release-token')) {
            throw new \Exception('A github token must be set first');
        }

        $client = Client::createWithHttpClient(new \GuzzleHttp\Client());
        $client->authenticate($settings->get('github-release-token'), null, \Github\Client::AUTH_ACCESS_TOKEN);

        $githubUsername = config('updater.github.username');
        $githubRepository = config('updater.github.repository');

        $release = $client->repository()->releases()->latest($githubUsername, $githubRepository);

        $this->release = $release;

        return $this->release['tag_name'];

    }
}
