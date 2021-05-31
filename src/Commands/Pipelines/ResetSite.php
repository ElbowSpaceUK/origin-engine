<?php

namespace OriginEngine\Commands\Pipelines;

use Illuminate\Support\Collection;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Pipeline\Pipeline;
use OriginEngine\Pipeline\Tasks\Utils\Closure;
use OriginEngine\Pipeline\Tasks\Feature\ClearActiveFeature;
use OriginEngine\Pipeline\Tasks\Git\CheckoutBranch;
use OriginEngine\Site\Site;

class ResetSite extends Pipeline
{

    public Site $site;

    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    public function tasks(): array
    {
        return [
            'check-changes-saved' => new Closure(
                function(Directory $directory, Collection $config) {
                    if(!IO::confirm('Have you saved your changes?', false)) {
                        throw new \Exception('Changes weren\'t saved, aborting.');
                    }
                }
            ),
            'checkout-branch' => new CheckoutBranch($this->site->getBlueprint()->getDefaultBranch()),
            'clear-feature' => new ClearActiveFeature($this->site),
        ];
    }

}
