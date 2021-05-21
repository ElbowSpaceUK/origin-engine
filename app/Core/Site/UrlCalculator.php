<?php

namespace App\Core\Site;

use App\Core\Helpers\Env\EnvRepository;
use App\Core\Helpers\WorkingDirectory\WorkingDirectory;

class UrlCalculator
{

    public static function calculate(string $instanceId, string $envFile = '.env'): string
    {
        $directory = WorkingDirectory::fromInstanceId($instanceId);

        $envRepository = new EnvRepository($directory);
        $env = $envRepository->get($envFile);

        $url = $env->getVariable('APP_URL');
        $port = $env->getVariable('APP_PORT');

        return sprintf('%s:%s', $url, $port);
    }

}
