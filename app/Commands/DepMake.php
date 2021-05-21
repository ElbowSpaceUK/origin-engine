<?php

namespace App\Commands;

use App\Core\Contracts\Command\SiteCommand;
use App\Core\Helpers\IO\IO;
use Illuminate\Support\Str;

class DepMake extends SiteCommand
{

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'dep:new
                            {--N|name= : The name of the composer dependency}
                            {--D|description= : A description for the new dependency}
                            {--R|repository= : The name of the repository to use}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create a new composer dependency';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // TODO Handle in a pipeline
        // TODO Think through the different ways of including this dependency.

        // Create the new folder

        // composer init

        $name = $this->getOrAskForOption(
            'name',
            IO::ask('What is the name of the dependency?'),
            fn($value) => is_string($value) && strlen($value) > 0 && Str::contains($value, '/')
        );

        $description = $this->getOrAskForOption(
            'description',
            IO::ask('What is the description of the dependency?'),
            fn($value) => is_string($value) && strlen($value) > 0
        );

        $site = $this->getSite();


        $this->info('Creating a new local dependency');

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

}
