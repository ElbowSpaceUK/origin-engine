<?php

namespace App\Core\Contracts\Site;

use App\Core\Site\Site;

interface SiteResolver
{

    public function setSite(Site $site): void;

    public function getSite(): Site;

    public function hasSite(): bool;

    public function clearSite(): void;

}
