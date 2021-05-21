<?php

namespace App\Core\Helpers\Composer\Schema\Schema;

use Illuminate\Contracts\Support\Arrayable;

class PackageRepositoryDistSchema implements Arrayable
{

    private string $url;

    private string $type;

    /**
     * PackageRepositoryDistSchema constructor.
     * @param string $url
     * @param string $type
     */
    public function __construct(string $url, string $type)
    {
        $this->url = $url;
        $this->type = $type;
    }


    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function toArray()
    {
        return [
            'url' => $this->url,
            'type' => $this->type
        ];
    }
}
