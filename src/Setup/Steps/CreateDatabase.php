<?php

namespace OriginEngine\Setup\Steps;

use OriginEngine\Contracts\Setup\SetupStep;
use OriginEngine\Helpers\Storage\Filesystem;

class CreateDatabase extends SetupStep
{

    public function run()
    {
        Filesystem::create()->touch($this->path());
    }

    private function path(): string
    {
        return Filesystem::database('atlas-cli.sqlite');
    }

    public function isSetup(): bool
    {
        return Filesystem::create()
            ->exists($this->path());
    }
}
