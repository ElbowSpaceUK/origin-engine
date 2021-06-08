<?php

namespace OriginEngine\Plugins\Stubs\Commands;

use Illuminate\Support\Collection;
use OriginEngine\Contracts\Command\Command;
use OriginEngine\Contracts\Command\FeatureCommand;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\PipelineConfig;
use OriginEngine\Pipeline\RunsPipelines;
use OriginEngine\Plugins\Stubs\Entities\Stub;
use OriginEngine\Plugins\Stubs\Entities\StubFile;
use OriginEngine\Plugins\Stubs\Pipelines\PublishStub;
use OriginEngine\Plugins\Stubs\StubMigrator;
use OriginEngine\Plugins\Stubs\StubDataCollector;
use OriginEngine\Plugins\Stubs\StubSaver;
use OriginEngine\Plugins\Stubs\StubStore;

class StubMake extends FeatureCommand
{
    use RunsPipelines;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'stub:make
                            {--S|stub= : The name of the stub to make}
                            {--L|location= : The directory relative to the project to save the stubs in}
                            {--force : Overwrite any files that already exist}
                            {--U|use-default : Use the default settings for the stub}
                            {--R|dry-run : Do not save any stub files, just output them to the terminal}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Use a stub';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(StubStore $stubStore, StubMigrator $stubCreator, StubDataCollector $dataCollector)
    {
        $stubName = $this->getOrAskForOption(
            'stub',
            fn() => $this->choice(
                'Which stub would you like to use?',
                collect($stubStore->getAllStubs())->map(fn(Stub $stub) => $stub->getName())->toArray()
            ),
            fn($value) => $value && $stubStore->hasStub($value)
        );

        $feature = $this->getFeature('Which feature should we copy the stub to?');

        $stub = $stubStore->getStub($stubName);

        $this->runPipeline(
            new PublishStub($stub),
            $feature->getSite()->getDirectory(),
            null,
            function(PipelineConfig $config) use ($stub) {
                $config->add('compile-stubs', 'data', $this->extractStubData($stub));
                $config->add('compile-stubs', 'use-default', $this->option('use-default'));
                $config->add('save-compiled-stubs', 'configuration', [
                    'location' => $this->option('location'),
                    'dry-run' => $this->option('dry-run'),
                    'force' => $this->option('force')
                ]);
            }
        );

        IO::success('Stubs saved');

    }

    private function extractStubData(Stub $stub): array
    {
        $stubData = $this->getConfigInput();

        foreach($stub->getStubFiles() as $stubFile) {
            foreach($stubFile->getReplacements() as $replacement) {
                if(array_key_exists($replacement->getVariableName(), $stubData)) {
                    $stubData[$replacement->getVariableName()] = $replacement->parseCommandInput($stubData[$replacement->getVariableName()]);
                }
            }
        }

        return $stubData;
    }


}
