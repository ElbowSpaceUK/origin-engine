<?php

namespace App\Core\Helpers\Composer\Schema\Schema;

use Illuminate\Contracts\Support\Arrayable;

class PackageRepositorySchema implements Arrayable
{

    private string $name;

    private string $version;

    private PackageRepositoryDistSchema $dist;

    private PackageRepositorySourceSchema $source;

    /**
     * PackageRepositorySchema constructor.
     * @param string $name
     * @param string $version
     * @param PackageRepositoryDistSchema $dist
     * @param PackageRepositorySourceSchema $source
     */
    public function __construct(string $name, string $version, PackageRepositoryDistSchema $dist, PackageRepositorySourceSchema $source)
    {
        $this->name = $name;
        $this->version = $version;
        $this->dist = $dist;
        $this->source = $source;
    }


    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * @return PackageRepositoryDistSchema
     */
    public function getDist(): PackageRepositoryDistSchema
    {
        return $this->dist;
    }

    /**
     * @param PackageRepositoryDistSchema $dist
     */
    public function setDist(PackageRepositoryDistSchema $dist): void
    {
        $this->dist = $dist;
    }

    /**
     * @return PackageRepositorySourceSchema
     */
    public function getSource(): PackageRepositorySourceSchema
    {
        return $this->source;
    }

    /**
     * @param PackageRepositorySourceSchema $source
     */
    public function setSource(PackageRepositorySourceSchema $source): void
    {
        $this->source = $source;
    }

    public function toArray()
    {
        return [
            'name' => $this->name,
            'version' => $this->version,
            'dist' => $this->dist,
            'source' => $this->source
        ];
    }
}
