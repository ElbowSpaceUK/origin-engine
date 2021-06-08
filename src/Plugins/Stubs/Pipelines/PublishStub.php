<?php

namespace OriginEngine\Plugins\Stubs\Pipelines;

use OriginEngine\Pipeline\Pipeline;
use OriginEngine\Pipeline\PipelineConfig;
use OriginEngine\Pipeline\PipelineHistory;
use OriginEngine\Pipeline\Task;
use OriginEngine\Pipeline\Tasks\Utils\CreateAndRunTask;
use OriginEngine\Plugins\Stubs\Entities\CompiledStub;
use OriginEngine\Plugins\Stubs\Entities\Stub;
use OriginEngine\Plugins\Stubs\Tasks\CompileStubs;
use OriginEngine\Plugins\Stubs\Tasks\SaveCompiledStubs;

class PublishStub extends Pipeline
{

    private Stub $stub;

    /**
     * The stubs once compiled
     *
     * @var CompiledStub[]
     */
    private array $compiledStubs;

    public function __construct(Stub $stub)
    {
        $this->stub = $stub;

        $this->after('compile-stubs', function(PipelineConfig $config, PipelineHistory $history, string $key) {
            $this->compiledStubs = $history->getOutput($key)->get('compiled');
        });
    }

    protected function tasks(): array
    {
        return [
            'compile-stubs' => new CompileStubs($this->stub),
            'save-compiled-stubs' => new CreateAndRunTask(
                fn() => new SaveCompiledStubs($this->stub, $this->compiledStubs),
                [],
                'Saving stubs',
                'Removing stubs'
            )
        ];
    }
}
