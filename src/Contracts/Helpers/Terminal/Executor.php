<?php

namespace OriginEngine\Contracts\Helpers\Terminal;

use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;

interface Executor
{

    public function execute(string $command): ?string;

    public function cd(WorkingDirectory $workingDirectory): Executor;

}
