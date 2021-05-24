<?php

namespace OriginEngine\Helpers\LaravelSail;

use OriginEngine\Helpers\Env\EnvRepository;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;

class UrlCalculator
{

    public static function calculate(string $directoryPath, string $envFile = '.env'): string
    {
        $directory = WorkingDirectory::fromDirectory($directoryPath);

        $envRepository = new EnvRepository($directory);
        $env = $envRepository->get($envFile);

        $url = $env->getVariable('APP_URL');
        $port = $env->getVariable('APP_PORT');

        return sprintf('%s:%s', $url, $port);
    }

}
