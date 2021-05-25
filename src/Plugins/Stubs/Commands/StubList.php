<?php

namespace OriginEngine\Plugins\Stubs\Commands;

use OriginEngine\Contracts\Command\Command;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Plugins\Stubs\Entities\Stub;
use OriginEngine\Plugins\Stubs\Entities\StubFile;
use OriginEngine\Plugins\Stubs\StubMigrator;
use OriginEngine\Plugins\Stubs\StubDataCollector;
use OriginEngine\Plugins\Stubs\StubSaver;
use OriginEngine\Plugins\Stubs\StubStore;

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
