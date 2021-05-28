<?php

namespace OriginEngine\Commands;

use OriginEngine\Commands\Pipelines\NewSite;
use OriginEngine\Contracts\Command\Command;
use OriginEngine\Contracts\Pipeline\PipelineRunner;
use OriginEngine\Contracts\Site\SiteBlueprintStore;
use OriginEngine\Contracts\Site\SiteRepository;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\PipelineConfig;
use OriginEngine\Pipeline\PipelineModifier;
use Illuminate\Support\Str;
use OriginEngine\Pipeline\RunsPipelines;
use OriginEngine\Site\SiteBlueprint;

class SiteNew extends Command
{
    use RunsPipelines;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'site:new
                            {--N|name= : The name of the site}
                            {--T|type= : The type of site to create}
                            {--D|description= : A description for the site}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create a new Atlas site.';


    /**
     * @var SiteRepository
     */
    protected $siteRepository;

    protected $directory = null;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(SiteBlueprintStore $blueprintStore, SiteRepository $siteRepository)
    {
        $this->siteRepository = $siteRepository;

        $name = $this->getSiteName();
        $directory = $this->getDirectory($name);
        $description = $this->getSiteDescription();
        $workingDirectory = Directory::fromDirectory($directory);

        $blueprintAlias = $this->getOrAskForOption(
            'type',
            fn() => $this->choice(
                'What kind of site would you like to make?',
                collect($blueprintStore->all())->mapWithKeys(fn(SiteBlueprint $blueprint, $alias) => [$alias => $blueprint->name()])->toArray()
            ),
            fn($value) => $blueprintStore->has($value)
        );
        $blueprint = $blueprintStore->get($blueprintAlias);


        $response = $this->runPipeline(new NewSite($name, $description, $blueprintAlias), $workingDirectory);

        if($response->allSuccessful()) {
            $this->getOutput()->success(sprintf('Installed a new instance of %s.', $blueprint->name()));
        } else {
            $this->getOutput()->error(sprintf('Installation of %s failed.', $blueprint->name()));
        }
    }

    private function getDirectory(string $name): string
    {
        if($this->directory === null) {
            $id = Str::kebab($name);
            $prefix = '';
            while($this->siteRepository->directoryExists($id . $prefix) === true) {
                if($prefix === '') {
                    $prefix = 1;
                } else {
                    $prefix++;
                }
            }
            $this->directory = trim($id . $prefix);
        }
        return $this->directory;
    }

    private function getSiteName(): string
    {
        return trim($this->getOrAskForOption(
            'name',
            fn() => $this->ask('Name this site in a couple of words.'),
            fn($value) => $value && is_string($value)
        ));
    }

    private function getSiteDescription(): string
    {
        return trim($this->getOrAskForOption(
            'description',
            fn() => $this->ask('Information to help you identify this site (optional).', ''),
            fn($value) => $value === '' || ($value && is_string($value) && strlen($value) < 250)
        ));
    }

}
