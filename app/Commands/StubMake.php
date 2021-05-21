<?php

namespace App\Commands;

use App\Core\Contracts\Command\Command;
use App\Core\Contracts\Command\FeatureCommand;
use App\Core\Helpers\IO\IO;
use App\Core\Helpers\Storage\Filesystem;
use App\Core\Helpers\WorkingDirectory\WorkingDirectory;
use App\Core\Stubs\Entities\Stub;
use App\Core\Stubs\Entities\StubFile;
use App\Core\Stubs\StubMigrator;
use App\Core\Stubs\StubDataCollector;
use App\Core\Stubs\StubSaver;
use App\Core\Stubs\StubStore;

class StubMake extends FeatureCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'stub:make
                            {--S|stub= : The name of the stub to make}
                            {--L|location= : The directory relative to the project to save the stubs in}
                            {--O|overwrite : Overwrite any files that already exist}
                            {--U|use-default : Use the default settings for the stub}
                            {--R|dry-run : Do not save any stub files, just output them to the terminal}
                            {--W|with=* : Data to pass to the stub. Separate the variable and value with an equals}';

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

        $workingDirectory = $this->getWorkingDirectory();

        $stub = $stubStore->getStub($stubName);

        $compiledStubs = $stubCreator->create($stub, $this->getStubData($stub), $this->option('use-default'));

        IO::info('Stubs compiled');

        $saver = StubSaver::in(WorkingDirectory::fromPath(
            Filesystem::append(
                $workingDirectory->path(),
                $this->option('location') ?? $stub->getDefaultLocation()
            )
        ))->force($this->option('overwrite'));

        foreach($compiledStubs as $stub) {
            $saver->save($stub, $this->option('dry-run'));
        }

        IO::success('Stubs saved');

    }

    private function getStubData(Stub $stub): array
    {
        $stubData = collect($this->option('with'))->mapWithKeys(function($data) {
            $parts = explode('=', $data);
            if(count($parts) !== 2) {
                throw new \Exception(sprintf('Data [%s] could not be parsed, please ensure you include both the variable name and value separated with an =.', $data));
            }
            return [$parts[0] => $parts[1]];
        })->toArray();;

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
