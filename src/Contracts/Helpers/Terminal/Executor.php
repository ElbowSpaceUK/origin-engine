<?php

namespace OriginEngine\Contracts\Helpers\Terminal;

use OriginEngine\Helpers\Directory\Directory;

interface Executor
{

    public function execute(string $command): ?string;

    public function cd(Directory $workingDirectory): Executor;

}
