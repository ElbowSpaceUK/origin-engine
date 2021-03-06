<?php

namespace OriginEngine\Commands\Pipelines;

use OriginEngine\Contracts\Site\SiteBlueprintStore;
use OriginEngine\Feature\Feature;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Pipeline\PipelineConfig;
use OriginEngine\Pipeline\PipelineHistory;
use OriginEngine\Pipeline\Tasks\Feature\CreateFeature;
use OriginEngine\Pipeline\Tasks\Site\CreateSite;
use OriginEngine\Pipeline\Tasks\Utils\Closure;
use OriginEngine\Pipeline\Tasks\Utils\RunPipeline;
use OriginEngine\Site\Site;
use OriginEngine\Pipeline\Pipeline;

class NewSite extends Pipeline
{

    private string $name;
    private ?string $description;
    protected string $siteAlias;

    public function __construct(string $name, ?string $description, string $siteAlias)
    {
        $this->name = $name;
        $this->description = $description;
        $this->siteAlias = $siteAlias;
    }

    public function tasks(): array
    {
        return [
            'install-site' => (new RunPipeline(app(SiteBlueprintStore::class)->get($this->siteAlias)->getInstallationPipeline()))
                ->setUpName('Installing the site')
                ->setDownName('Removing the site'),
            'create-site' => (new CreateSite($this->name, $this->description, $this->siteAlias))
                ->setUpName('Saving site meta data')
                ->setDownName('Removing site meta data'),
        ];
    }

}
