<?php

namespace App\Core\Site;

use App\Core\Contracts\Helpers\Settings\SettingRepository;
use App\Core\Contracts\Site\SiteResolver;

class SettingsSiteResolver implements SiteResolver
{
    public const SETTING_KEY = 'current-site';

    /**
     * @var SettingRepository
     */
    private SettingRepository $settingRepository;

    public function __construct(SettingRepository $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    public function setSite(Site $site): void
    {
        $this->settingRepository->set(static::SETTING_KEY, $site->getId());
    }

    public function getSite(): Site
    {
        if($this->hasSite()) {
            return Site::findOrFail(
                $this->settingRepository->get(static::SETTING_KEY)
            );
        }
        throw new \Exception('No site is set');
    }

    public function hasSite(): bool
    {
        return $this->settingRepository->has(static::SETTING_KEY);
    }

    public function clearSite(): void
    {
        $this->settingRepository->forget(static::SETTING_KEY);
    }
}
