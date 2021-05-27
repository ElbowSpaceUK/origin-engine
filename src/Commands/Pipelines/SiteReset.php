<?php

namespace OriginEngine\Commands\Pipelines;

use Cz\Git\GitException;
use Cz\Git\GitRepository;
use Illuminate\Support\Collection;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Pipeline\Pipeline;
use OriginEngine\Pipeline\Task;
use OriginEngine\Pipeline\Tasks\Closure;
use OriginEngine\Pipeline\Tasks\Utils\Repeater;
use OriginEngine\Plugins\Dependencies\Contracts\LocalPackageRepository;
use OriginEngine\Plugins\Dependencies\LocalPackage;

class SiteReset extends Pipeline
{

    public function __construct()
    {
    }

    public function getTasks(): array
    {
        return [
            'check-changes-saved' => new Closure(
                function(Collection $config, Directory $directory) {
                    if(!IO::confirm('Have you saved your changes?', false)) {
                        throw new \Exception('Changes weren\'t saved, aborting.');
                    }
                }
            ),
            'checkout-branch' => '',
            'clear-feature' => '',
        ];
    }

//     Make all packages remote
//$branch = $site->getBlueprint()->getBranch();
//$currentFeature = $site->getCurrentFeature();
//$workingDirectory = $site->getDirectory();
//if($currentFeature !== null) {
//$packages = app(LocalPackageRepository::class)->getAllThroughFeature($currentFeature->getId());
//if(count($packages) > 0) {
//foreach($packages as $package) {
//$localPackageHelper->makeRemote($package, $workingDirectory);
//}
//}
//}
//
//// Checkout (or create) branch
//$git = new GitRepository($site->getDirectory()->path());
//try {
//    $git->checkout($branch);
//} catch (GitException $e) {
//    $git->createBranch($branch, true);
//}
//
//// Clear the default feature
//$featureResolver->clearFeature();

}