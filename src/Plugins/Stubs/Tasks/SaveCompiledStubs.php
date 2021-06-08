<?php

namespace OriginEngine\Plugins\Stubs\Tasks;

use Illuminate\Support\Collection;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Pipeline\Task;
use OriginEngine\Pipeline\TaskResponse;
use OriginEngine\Plugins\Stubs\Entities\CompiledStub;
use OriginEngine\Plugins\Stubs\Entities\Stub;
use OriginEngine\Plugins\Stubs\StubSaver;

class SaveCompiledStubs extends Task
{

    /**
     * @param array|CompiledStub[] $compiledStubs
     */
    public function __construct(Stub $stub, array $compiledStubs = [])
    {
        parent::__construct([
            'stub' => $stub,
            'compiled-stubs' => $compiledStubs,
            'location' => $stub->getDefaultLocation(),
            'force' => false,
            'dry-run' => false
        ]);
    }

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        $saver = StubSaver::in(Directory::fromFullPath(
            Filesystem::append(
                $workingDirectory->path(),
                $config->get('location')
            )
        ))->force($config->get('force'));

        foreach($config->get('compiled-stubs') as $stub) {
            $saver->save($stub, $config->get('dry-run'));
        }

        return $this->succeeded();
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        // TODO: Implement undo() method.
    }

    protected function upName(Collection $config): string
    {
        return 'Saving stubs';
    }

    protected function downName(Collection $config): string
    {
        return 'Removing stubs';
    }
}
