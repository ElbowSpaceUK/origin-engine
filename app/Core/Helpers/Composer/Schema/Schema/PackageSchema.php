<?php

namespace App\Core\Helpers\Composer\Schema\Schema;

use Illuminate\Contracts\Support\Arrayable;

class PackageSchema implements Arrayable
{

    private string $name;

    private string $version;

    /**
     * PackageSchema constructor.
     * @param string $name
     * @param string $version
     */
    public function __construct(string $name, string $version)
    {
        $this->name = $name;
        $this->version = $version;
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

    public function toArray()
    {
        return [
            'name' => $this->name,
            'version' => $this->version
        ];
    }
}
