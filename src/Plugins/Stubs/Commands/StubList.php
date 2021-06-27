<?php

namespace OriginEngine\Plugins\Stubs\Commands;

use OriginEngine\Command\Command;
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

}
