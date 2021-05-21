<?php

namespace App\Core\Stubs\Registrar;

use App\Core\Stubs\Entities\Stub;
use App\Core\Stubs\Entities\StubFile;
use App\Core\Stubs\StubStore;

class StubRegistrar
{

    /**
     * @var Stub
     */
    private Stub $stub;

    public function __construct(Stub $stub)
    {
        $this->stub = $stub;
    }

    public static function registerStub(string $name, string $description, string $defaultLocation = null): StubRegistrar
    {
        $stub = new Stub();
        $stub->setName($name);
        $stub->setDescription($description);
        $stub->setDefaultLocation($defaultLocation);
        return new static($stub);
    }

    public function addFile(StubFileRegistrar $fileRegistrar): StubRegistrar
    {
        $stubFiles = $this->stub->getStubFiles();
        $stubFiles[] = $fileRegistrar->getStubFile();
        $this->stub->setStubFiles($stubFiles);
        return $this;
    }

    protected function save()
    {
        app(StubStore::class)->registerStub($this->stub);
    }

    public function __destruct()
    {
        $this->save();
    }
}
