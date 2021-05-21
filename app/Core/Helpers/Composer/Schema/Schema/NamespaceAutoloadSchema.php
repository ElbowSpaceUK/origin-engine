<?php

namespace App\Core\Helpers\Composer\Schema\Schema;

class NamespaceAutoloadSchema
{

    /**
     * @var string
     */
    private string $namespace;

    /**
     * @var array|string
     */
    private $paths;

    /**
     * NamespaceAutoloadSchema constructor.
     * @param string $namespace
     * @param array|string $paths
     */
    public function __construct(string $namespace, $paths)
    {
        $this->namespace = $namespace;
        $this->paths = $paths;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     */
    public function setNamespace(string $namespace): void
    {
        $this->namespace = $namespace;
    }

    /**
     * @return array|string
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * @param array|string $paths
     */
    public function setPaths($paths): void
    {
        $this->paths = $paths;
    }
}
