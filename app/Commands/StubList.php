<?php

namespace App\Commands;

use App\Core\Contracts\Command\Command;
use App\Core\Helpers\IO\IO;
use App\Core\Helpers\Storage\Filesystem;
use App\Core\Helpers\WorkingDirectory\WorkingDirectory;
use App\Core\Stubs\Entities\Stub;
use App\Core\Stubs\Entities\StubFile;
use App\Core\Stubs\StubMigrator;
use App\Core\Stubs\StubDataCollector;
use App\Core\Stubs\StubSaver;
use App\Core\Stubs\StubStore;

class StubList extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'stub:list';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List the available stubs';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(StubStore $stubStore)
    {
        $stubs = collect($stubStore->getAllStubs());

        $this->table(
            ['Name', 'Description'],
            $stubs->map(function(Stub $stub) {
                return [
                    $stub->getName(),
                    $stub->getDescription()
                ];
            })
        );
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
