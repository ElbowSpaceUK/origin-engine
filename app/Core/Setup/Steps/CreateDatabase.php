<?php

namespace App\Core\Setup\Steps;

use App\Core\Contracts\Setup\SetupStep;
use App\Core\Helpers\Storage\Filesystem;

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
