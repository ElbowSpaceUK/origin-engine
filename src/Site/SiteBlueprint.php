<?php

namespace OriginEngine\Site;

use OriginEngine\Pipeline\Pipeline;

abstract class SiteBlueprint
{

    protected string $defaultBranch;

    abstract public function name(): string;

    abstract public function getUrls(Site $site): array;

    abstract public function getStatus(Site $site): string;

    abstract public function getInstallationPipeline(): Pipeline;

    abstract public function getUninstallationPipeline(): Pipeline;

    abstract public function getSiteUpPipeline(): Pipeline;

    abstract public function getSiteDownPipeline(): Pipeline;

    public function getDefaultBranch(): string
    {
        if(isset($this->defaultBranch)) {
            return $this->defaultBranch;
        }
        throw new \Exception(sprintf('The site [%s] does not support features', $this->name()));
    }

}
