<?php

namespace App\Core\Contracts\Helpers\Composer;

use App\Core\Helpers\Composer\Schema\Schema\ComposerSchema;

interface Operation
{

    public function perform(ComposerSchema $composerSchema): ComposerSchema;

}
