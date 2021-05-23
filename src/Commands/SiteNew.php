<?php

namespace OriginEngine\Commands;

use OriginEngine\Contracts\Command\Command;
use OriginEngine\Contracts\Pipeline\PipelineRunner;
use OriginEngine\Contracts\Site\SiteBlueprintStore;
use OriginEngine\Contracts\Site\SiteRepository;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;
use OriginEngine\Pipeline\PipelineConfig;
use OriginEngine\Pipeline\PipelineManager;
use Illuminate\Support\Str;
use OriginEngine\Site\SiteBlueprint;

class SiteNew extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'site:new
                            {--N|name= : The name of the site}
                            {--T|type= : The type of site to create}
                            {--D|description= : A description for the site}
                            {--C|config=* : Data to pass to the installation pipelione. Separate the variable and value with an equals}';

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

    protected $instanceId = null;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(SiteBlueprintStore $blueprintStore, SiteRepository $siteRepository, PipelineRunner $runner)
    {
        $this->siteRepository = $siteRepository;
        $this->info('Creating a new site');

        $name = $this->getInstanceName();
        $instanceId = $this->getInstanceId($name);
        $description = $this->getInstanceDescription();

        $workingDirectory = WorkingDirectory::fromInstanceId($instanceId);

        $blueprintAlias = $this->getOrAskForOption(
            'type',
            fn() => $this->choice(
                'What kind of site would you like to make?',
                collect($blueprintStore->all())->mapWithKeys(fn(SiteBlueprint $blueprint, $alias) => [$alias => $blueprint->name()])->toArray()
            ),
            fn($value) => $blueprintStore->has($value)
        );
        $blueprint = $blueprintStore->get($blueprintAlias);

        $config = new PipelineConfig(collect($this->option('config'))->mapWithKeys(function($data) {
            $parts = explode('=', $data);
            if(count($parts) !== 2) {
                throw new \Exception(sprintf('Data [%s] could not be parsed, please ensure you include both the variable name and value separated with an =.', $data));
            }
            return [$parts[0] => $parts[1]];
        })->toArray());

        $response = $runner->run($blueprint->getInstallationPipeline(), $config, $workingDirectory);

        if($response->allSuccessful()) {
            $this->getOutput()->success(sprintf('Installed a new instance of %s.', $blueprint->name()));
        } else {
            $this->getOutput()->error(sprintf('Installation of %s failed.', $blueprint->name()));
        }
        dd($response);
//        try {
//            $installManager->driver(
//                $this->option('repository')
//            )->install($workingDirectory);
//            $site = $siteRepository->create(
//                $instanceId,
//                $name,
//                $description,
//                $this->option('repository')
//            );
//        } catch (\Exception $e) {
//            if($this->output->isVerbose()) {
//                throw $e;
//            }
//            IO::error('Install failed: ' . $e->getMessage());
//            return;
//        }
//
//        $this->getOutput()->success(sprintf('Installed a new Atlas instance.'));
    }

    private function getInstanceId(string $name): string
    {
        if($this->instanceId === null) {
            $id = Str::kebab($name);
            $prefix = '';
            while($this->siteRepository->instanceIdExists($id . $prefix) === true) {
                if($prefix === '') {
                    $prefix = 1;
                } else {
                    $prefix++;
                }
            }
            $this->instanceId = trim($id . $prefix);
        }
        return $this->instanceId;
    }

    private function getInstanceName(): string
    {
        return trim($this->getOrAskForOption(
            'name',
            fn() => $this->ask('Name this site in a couple of words.'),
            fn($value) => $value && is_string($value)
        ));
    }

    private function getInstanceDescription(): string
    {
        return trim($this->getOrAskForOption(
            'description',
            fn() => $this->ask('Information to help you identify this site (optional).', ''),
            fn($value) => $value === '' || ($value && is_string($value) && strlen($value) < 250)
        ));
    }

}
