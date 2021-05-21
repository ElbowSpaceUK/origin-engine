<?php

namespace App\Core\Stubs\Entities;

class Stub
{

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $description;

    /**
     * @var StubFile[]
     */
    private array $stubFiles = [];

    /**
     * @var string
     */
    private string $defaultLocation;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Stub
     */
    public function setName(string $name): Stub
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Stub
     */
    public function setDescription(string $description): Stub
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return StubFile[]
     */
    public function getStubFiles(): array
    {
        return $this->stubFiles;
    }

    /**
     * @param StubFile[] $stubFiles
     * @return Stub
     */
    public function setStubFiles(array $stubFiles): Stub
    {
        $this->stubFiles = $stubFiles;
        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultLocation(): string
    {
        return $this->defaultLocation;
    }

    /**
     * @param string $defaultLocation
     * @return Stub
     */
    public function setDefaultLocation(string $defaultLocation): Stub
    {
        $this->defaultLocation = $defaultLocation;
        return $this;
    }


}
