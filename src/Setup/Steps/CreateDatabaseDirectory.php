<?php

namespace OriginEngine\Setup\Steps;

use OriginEngine\Contracts\Setup\SetupStep;
use OriginEngine\Helpers\Storage\Filesystem;

class CreateDatabaseDirectory extends SetupStep
{

    public function run()
    {
        $directory = Filesystem::database();

        if(! is_dir($directory) && !mkdir($directory, 0777, true)) {
            throw new \Exception(sprintf('Could not create directory %s.', $directory));
        }
    }

    public function isSetup(): bool
    {
        return Filesystem::create()
            ->exists(
                Filesystem::database()
            );
    }
}
