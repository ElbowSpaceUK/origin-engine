<?php

namespace OriginEngine\Contracts\Helpers\Composer;

use OriginEngine\Helpers\Composer\Schema\Schema\ComposerSchema;

interface Operation
{

    public function perform(ComposerSchema $composerSchema): ComposerSchema;

}
