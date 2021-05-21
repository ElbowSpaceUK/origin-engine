<?php

namespace App\Core\Stubs;

use App\Core\Stubs\Entities\Stub;

class StubStore
{

    private array $stubs = [];

    /**
     * Register a stub
     *
     * @param Stub $stub
     * @return void
     * @throws \Exception
     */
    public function registerStub(Stub $stub): void
    {
        if($this->hasStub($stub->getName())) {
            throw new \Exception(sprintf('Stub %s already exists', $stub->getName()));
        }
        $this->stubs[$stub->getName()] = $stub;
    }

    /**
     * Get all the stubs registered
     *
     * @return Stub[]
     */
    public function getAllStubs(): array
    {
        return $this->stubs;
    }

    /**
     * Check if the given stub is registered
     *
     * @param string $name
     * @return bool
     */
    public function hasStub(string $name): bool
    {
        return array_key_exists($name, $this->stubs);
    }

    /**
     * Get a stub by name
     *
     * @param string $name
     * @return Stub
     * @throws \Exception
     */
    public function getStub(string $name): Stub
    {
        if($this->hasStub($name)) {
            return $this->stubs[$name];
        }
        throw new \Exception(
            sprintf('Stub %s could not be found', $name)
        );
    }
}
