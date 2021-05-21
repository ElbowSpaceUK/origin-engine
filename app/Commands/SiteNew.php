<?php

namespace App\Commands;

use App\Core\Contracts\Command\Command;
use App\Core\Contracts\Site\SiteRepository;
use App\Core\Helpers\IO\IO;
use App\Core\Helpers\WorkingDirectory\WorkingDirectory;
use App\Core\Pipeline\PipelineManager;
use Illuminate\Support\Str;

class SiteNew extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'site:new
                            {--N|name= : The name of the site}
                            {--R|repository=cms : Takes values of `cms` or `frontend`}
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

    protected $instanceId = null;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(PipelineManager $installManager, SiteRepository $siteRepository)
    {
        $this->siteRepository = $siteRepository;
        $this->info('Creating a new site');

        $name = trim($this->getInstanceName());
        $instanceId = trim($this->getInstanceId($name));
        $description = trim($this->getInstanceDescription());

        $workingDirectory = WorkingDirectory::fromInstanceId($instanceId);

        try {
            $installManager->driver(
                $this->option('repository')
            )->install($workingDirectory);
            $site = $siteRepository->create(
                $instanceId,
                $name,
                $description,
                $this->option('repository')
            );
        } catch (\Exception $e) {
            if($this->output->isVerbose()) {
                throw $e;
            }
            IO::error('Install failed: ' . $e->getMessage());
            return;
        }

        $this->getOutput()->success(sprintf('Installed a new Atlas instance.'));
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
            $this->instanceId = $id . $prefix;
        }
        return $this->instanceId;
    }

    private function getInstanceName(): string
    {
        return $this->getOrAskForOption(
            'name',
            fn() => $this->ask('Name this site in a couple of words.'),
            fn($value) => $value && is_string($value)
        );
    }

    private function getInstanceDescription(): string
    {
        return $this->getOrAskForOption(
            'description',
            fn() => $this->ask('Information to help you identify this site (optional).', ''),
            fn($value) => $value === '' || ($value && is_string($value) && strlen($value) < 250)
        );
    }

}
