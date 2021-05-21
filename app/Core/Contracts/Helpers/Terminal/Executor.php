<?php

namespace App\Core\Contracts\Helpers\Terminal;

use App\Core\Helpers\WorkingDirectory\WorkingDirectory;

interface Executor
{

    public function execute(string $command): ?string;

    public function cd(WorkingDirectory $workingDirectory): Executor;

}
