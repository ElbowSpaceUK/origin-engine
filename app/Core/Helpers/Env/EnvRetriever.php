<?php

namespace App\Core\Helpers\Env;

use Dotenv\Dotenv;

class EnvRetriever
{

    protected string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function get(string $filename): Dotenv
    {
        return Dotenv::createMutable($this->path, $filename);
    }

}
