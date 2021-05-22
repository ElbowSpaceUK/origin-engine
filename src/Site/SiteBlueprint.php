<?php

namespace OriginEngine\Site;

use OriginEngine\Pipeline\Pipeline;

abstract class SiteBlueprint
{
    const STATUS_MISSING = 'missing';

    const STATUS_READY = 'ready';

    const STATUS_DOWN = 'down';

    abstract public function name(): string;

    abstract public function getUrl(Site $site): string;

    abstract public function getStatus(Site $site): string;

    abstract public function getInstallationPipeline(): Pipeline;

    abstract public function getUninstallationPipeline(): Pipeline;

    abstract public function getSiteUpPipeline(): Pipeline;

    abstract public function getSiteDownPipeline(): Pipeline;

}
