<?php

namespace OriginEngine\Plugins\Stubs;

use OriginEngine\Plugins\Stubs\Entities\CompiledStub;

class StubMigrator
{

    /**
     * @var StubFileCompiler
     */
    private StubFileCompiler $compiler;
    /**
     * @var StubDataCollector
     */
    private StubDataCollector $stubDataCollector;

    public function __construct(StubFileCompiler $compiler, StubDataCollector $stubDataCollector)
    {
        $this->compiler = $compiler;
        $this->stubDataCollector = $stubDataCollector;
    }

    /**
     * @param Entities\Stub $stub
     * @param array $data
     * @param bool $useDefault
     * @return array|CompiledStub[]
     */
    public function create(Entities\Stub $stub, array $data = [], bool $useDefault = false): array
    {
        $data = $this->stubDataCollector->collect($stub, $data, $useDefault);

        $compiled = [];
        foreach($data->getStubFiles() as $stubFile) {
            $compiled[] = $this->compiler->compile($stubFile, $data->getData());
        }
        return $compiled;
    }
}
