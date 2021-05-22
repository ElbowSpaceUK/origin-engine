<?php

namespace OriginEngine\Contracts\Site;

use OriginEngine\Site\Site;

interface SiteResolver
{

    public function setSite(Site $site): void;

    public function getSite(): Site;

    public function hasSite(): bool;

    public function clearSite(): void;

}
