<?php

namespace OriginEngine\Commands\Feature;

use OriginEngine\Commands\Pipelines\NewFeature;
use OriginEngine\Command\SiteCommand;
use OriginEngine\Contracts\Site\SiteRepository;
use OriginEngine\Feature\Feature;
use OriginEngine\Helpers\Directory\Directory;
use Cz\Git\GitException;
use Cz\Git\GitRepository;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Pipeline\RunsPipelines;

class FeatureNew extends SiteCommand
{
    use RunsPipelines;
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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $site = $this->getSite('Which site would you like to create the feature on?');

        $featureName = trim($this->getFeatureName());
        $featureDescription = trim($this->getFeatureDescription());
        $featureType = trim($this->getFeatureChangeType());
        $branchName = $this->getOrAskForOption(
            'branch',
            fn() => IO::ask('What should we name the branch?', Feature::getDefaultBranchName($featureType, $featureName)),
            fn($value) => $value && strlen($value) > 0
        );

        $history = $this->runPipeline(new NewFeature($site, $featureName, $featureDescription, $featureType, $branchName), $site->getDirectory());

    }

    private function getFeatureName(): string
    {
        return $this->getOrAskForOption(
            'name',
            fn() => IO::ask('Name this feature in a couple of words'),
            fn($value) => $value && is_string($value)
        );
    }

    private function getFeatureDescription(): string
    {
        return $this->getOrAskForOption(
            'description',
            fn() => IO::ask('Describe what this feature will do', ''),
            fn($value) => $value === '' || ($value && is_string($value) && strlen($value) < 250)
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
                IO::choice('What kind of change is this?', array_values($allowedTypes)),
                $allowedTypes
            ),
            fn($value) => $value && in_array($value, array_keys($allowedTypes))
        );
    }

}
