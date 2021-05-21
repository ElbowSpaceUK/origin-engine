<?php

namespace App\Core\Stubs\Entities;

use App\Core\Contracts\Stubs\StubReplacement;
use Closure;

class StubFile
{

    /**
     * The absolute path to the stub file
     *
     * @var string
     */
    private string $stubPath;

    /**
     * The location to publish the stubs
     *
     * @var string|null
     */
    private ?string $location = null;

    /**
     * @var string|Closure
     */
    private $fileName;

    private ?Closure $showIf = null;

    /**
     * @return Closure|null
     */
    public function getShowIf(): ?Closure
    {
        return $this->showIf;
    }

    /**
     * Set the function to determine whether to show the stub file or not.
     *
     * Will be given any data that has so far been resolved
     *
     * @param Closure|null $showIf
     * @return StubFile
     */
    public function setShowIf(?Closure $showIf): StubFile
    {
        $this->showIf = $showIf;
        return $this;
    }

    /**
     * @var StubReplacement[]
     */
    private array $replacements = [];

    /**
     * @return StubReplacement[]
     */
    public function getReplacements(): array
    {
        return $this->replacements;
    }

    /**
     * @param StubReplacement[] $replacements
     * @return StubFile
     */
    public function setReplacements(array $replacements): StubFile
    {
        $this->replacements = $replacements;
        return $this;
    }

    /**
     * @return string
     */
    public function getStubPath(): string
    {
        return $this->stubPath;
    }

    /**
     * @param string $stubPath
     * @return StubFile
     */
    public function setStubPath(string $stubPath): StubFile
    {
        $this->stubPath = $stubPath;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * @param string|null $location
     * @return StubFile
     */
    public function setLocation(?string $location = null): StubFile
    {
        $this->location = $location;
        return $this;
    }

    /**
     * @return string|\Closure
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param string|\Closure $fileName
     * @return StubFile
     */
    public function setFileName($fileName): StubFile
    {
        if(!is_callable($fileName) && !is_string($fileName)) {
            throw new \Exception(
                sprintf('The filename should be a string, %s given', $fileName)
            );
        }
        $this->fileName = $fileName;
        return $this;
    }

    public function showIf(array $data): bool
    {
        if($this->getShowIf() !== null) {
            return $this->getShowIf()($data);
        }
        return true;
    }

}
