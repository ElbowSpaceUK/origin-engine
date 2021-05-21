<?php

namespace App\Core\Helpers\Composer\Schema\Schema;

use Illuminate\Contracts\Support\Arrayable;

class AutoloadSchema implements Arrayable
{

    /**
     * @var array|NamespaceAutoloadSchema[]
     */
    private array $psr4;

    /**
     * @var array|NamespaceAutoloadSchema[]
     */
    private array $psr0;

    /**
     * @var array|string[]
     */
    private array $classmap;

    /**
     * @var array|string[]
     */
    private array $files;

    /**
     * @var array|string[]
     */
    private array $excludeFromClassmap;

    /**
     * AutoloadSchema constructor.
     * @param array|NamespaceAutoloadSchema[] $psr4
     * @param array|NamespaceAutoloadSchema[] $psr0
     * @param array|string[] $classmap
     * @param array|string[] $files
     * @param array|string[] $excludeFromClassmap
     */
    public function __construct(array $psr4 = [],
                                array $psr0 = [],
                                array $classmap = [],
                                array $files = [],
                                array $excludeFromClassmap = [])
    {
        $this->psr4 = $psr4;
        $this->psr0 = $psr0;
        $this->classmap = $classmap;
        $this->files = $files;
        $this->excludeFromClassmap = $excludeFromClassmap;
    }

    /**
     * @return array
     */
    public function getPsr4(): array
    {
        return $this->psr4;
    }

    /**
     * @param array $psr4
     */
    public function setPsr4(array $psr4): void
    {
        $this->psr4 = $psr4;
    }

    /**
     * @return array
     */
    public function getPsr0(): array
    {
        return $this->psr0;
    }

    /**
     * @param array $psr0
     */
    public function setPsr0(array $psr0): void
    {
        $this->psr0 = $psr0;
    }

    /**
     * @return array|string[]
     */
    public function getClassmap(): array
    {
        return $this->classmap;
    }

    /**
     * @param array|string[] $classmap
     */
    public function setClassmap(array $classmap): void
    {
        $this->classmap = $classmap;
    }

    /**
     * @return array|string[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @param array|string[] $files
     */
    public function setFiles(array $files): void
    {
        $this->files = $files;
    }

    /**
     * @return array|string[]
     */
    public function getExcludeFromClassmap(): array
    {
        return $this->excludeFromClassmap;
    }

    /**
     * @param array|string[] $excludeFromClassmap
     */
    public function setExcludeFromClassmap(array $excludeFromClassmap): void
    {
        $this->excludeFromClassmap = $excludeFromClassmap;
    }

    public function toArray()
    {
        return collect([
            'psr-4' => collect($this->psr4)->mapWithKeys(fn(NamespaceAutoloadSchema $namespaceSchema) => [$namespaceSchema->getNamespace() => $namespaceSchema->getPaths()])->toArray(),
            'psr-0' => collect($this->psr0)->mapWithKeys(fn(NamespaceAutoloadSchema $namespaceSchema) => [$namespaceSchema->getNamespace() => $namespaceSchema->getPaths()])->toArray(),
            'classmap' => $this->classmap,
            'files' => $this->files,
            'exclude-from-classmap' => $this->excludeFromClassmap,
        ])->filter(fn($val) => $val !== [] && $val !== null && ($val instanceof Collection ? $val->count() > 0 : true))->toArray();
    }
}
