<?php

namespace App\Core\Helpers\Composer\Schema\Schema;

use Illuminate\Contracts\Support\Arrayable;

class FundingSchema implements Arrayable
{

    private string $type;

    private string $url;

    /**
     * FundingSchema constructor.
     * @param string $type
     * @param string $url
     */
    public function __construct(string $type, string $url)
    {
        $this->type = $type;
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

    public function toArray()
    {
        return [
            'type' => $this->type,
            'url' => $this->url
        ];
    }
}
