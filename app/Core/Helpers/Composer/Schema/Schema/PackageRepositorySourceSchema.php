<?php

namespace App\Core\Helpers\Composer\Schema\Schema;

use Illuminate\Contracts\Support\Arrayable;

class PackageRepositorySourceSchema implements Arrayable
{

    private string $url;

    private string $type;

    private string $reference;

    /**
     * PackageRepositorySourceSchema constructor.
     * @param string $url
     * @param string $type
     * @param string $reference
     */
    public function __construct(string $url, string $type, string $reference)
    {
        $this->url = $url;
        $this->type = $type;
        $this->reference = $reference;
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

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     */
    public function setReference(string $reference): void
    {
        $this->reference = $reference;
    }

    public function toArray()
    {
        return [
            'url' => $this->url,
            'type' => $this->type,
            'reference' => $this->reference
        ];
    }
}
