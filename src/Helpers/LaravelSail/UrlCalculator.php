<?php

namespace OriginEngine\Helpers\LaravelSail;

use OriginEngine\Helpers\Env\EnvRepository;
use OriginEngine\Helpers\Directory\Directory;

class UrlCalculator
{

    public static function calculate(string $directoryPath, string $envFile = '.env'): string
    {
        $directory = Directory::fromDirectory($directoryPath);

        $envRepository = new EnvRepository($directory);
        $env = $envRepository->get($envFile);

        $url = $env->getVariable('APP_URL');
        $port = $env->getVariable('APP_PORT');

        return sprintf('%s:%s', $url, $port);
    }

}
