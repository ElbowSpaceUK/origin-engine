<?php

namespace OriginEngine\Plugins\Stubs\Tasks;

use Illuminate\Support\Collection;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\Task;
use OriginEngine\Pipeline\TaskResponse;
use OriginEngine\Plugins\Stubs\Entities\Stub;
use OriginEngine\Plugins\Stubs\StubMigrator;

class CompileStubs extends Task
{

    public function __construct(Stub $stub)
    {
        parent::__construct([
            'stub' => $stub,
            'use-default' => false,
            'data' => []
        ]);
    }

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        /** @var StubMigrator $stubCreator */
        $stubCreator = app(StubMigrator::class);

        $compiledStubs = $stubCreator->create(
            $config->get('stub'),
            $config->get('data'),
            $config->get('use-default'));

        $this->export('compiled', $compiledStubs);

        return $this->succeeded();
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        // No down method
    }

    protected function upName(Collection $config): string
    {
        return 'Compiling stubs';
    }

    protected function downName(Collection $config): string
    {
        return 'Uncompiling stubs';
    }

}

