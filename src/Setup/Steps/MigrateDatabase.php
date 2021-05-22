<?php

namespace OriginEngine\Setup\Steps;

use OriginEngine\Contracts\Setup\SetupStep;
use Illuminate\Database\Console\Migrations\MigrateCommand;
use Illuminate\Support\Facades\Artisan;

class MigrateDatabase extends SetupStep
{

    public function run()
    {
        Artisan::call(MigrateCommand::class, ['--force' => true]);
    }

    public function isSetup(): bool
    {
        return false;
    }
}
