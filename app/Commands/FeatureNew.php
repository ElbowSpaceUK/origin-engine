<?php

namespace App\Commands;

use App\Core\Contracts\Command\SiteCommand;
use App\Core\Contracts\Feature\FeatureRepository;
use App\Core\Contracts\Feature\FeatureResolver;
use App\Core\Contracts\Site\SiteRepository;
use App\Core\Feature\Feature;
use App\Core\Helpers\IO\IO;
use App\Core\Helpers\WorkingDirectory\WorkingDirectory;
use App\Core\Pipeline\PipelineManager;
use Cz\Git\GitException;
use Cz\Git\GitRepository;
use Illuminate\Support\Str;
use App\Core\Contracts\Command\Command;

class FeatureNew extends SiteCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'feature:new
                            {--N|name= : The name of the feature}
                            {--D|description= : A description for the feature}
                            {--T|type= : The type of change}
                            {--B|branch= : The name of the branch to use}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create a new feature in a site.';


    /**
     * @var SiteRepository
     */
    protected $siteRepository;

    protected $instanceId = null;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(FeatureRepository $featureRepository, FeatureResolver $featureResolver)
    {
        $this->info('Creating a new feature');

        $site = $this->getSite('Which site would you like to create the feature on?');
        $featureName = trim($this->getFeatureName());
        $featureDescription = trim($this->getFeatureDescription());
        $featureType = trim($this->getFeatureChangeType());

        $branchName = $this->getOrAskForOption(
            'branch',
            fn() => $this->ask('What should we name the branch?', Feature::getDefaultBranchName($featureType, $featureName)),
            fn($value) => $value && strlen($value) > 0
        );

        $feature = $featureRepository->create(
            $site->getId(),
            $featureName,
            $featureDescription,
            $featureType,
            $branchName
        );

        $this->info('Setting up new feature');

        $this->call(FeatureUse::class, ['--feature' => $feature->getId()]);

        $this->getOutput()->success(sprintf('Created feature [%s].', $featureName));
    }

    private function getFeatureName(): string
    {
        return $this->getOrAskForOption(
            'name',
            fn() => $this->ask('Name this feature in a couple of words'),
            fn($value) => $value && is_string($value)
        );
    }

    private function getFeatureDescription(): string
    {
        return $this->getOrAskForOption(
            'name',
            fn() => $this->ask('Describe what this feature will do'),
            fn($value) => $value && is_string($value) && strlen($value) < 250
        );
    }

    private function getFeatureChangeType()
    {
        $allowedTypes = [
            'added' => 'Added (for new features)',
            'changed' => 'Changed (for changes in existing functionality)',
            'deprecated' => 'Deprecated (for soon-to-be removed features)',
            'removed' => 'Removed (for now removed features)',
            'fixed' => 'Fixed (for any bug fixes)',
            'security' => 'Security (in case of vulnerabilities)'
        ];

        return $this->getOrAskForOption(
            'type',
            fn() => array_search(
                $this->choice('What kind of change is this?', array_values($allowedTypes)),
                $allowedTypes
            ),
            fn($value) => $value && in_array($value, array_keys($allowedTypes))
        );
    }

    private function checkoutBranch(string $branchName, WorkingDirectory $path)
    {
        $git = new GitRepository($path->path());
        try {
            $git->checkout($branchName);
        } catch (GitException $e) {
            $git->createBranch($branchName, true);
        }
        return true;
    }

}
