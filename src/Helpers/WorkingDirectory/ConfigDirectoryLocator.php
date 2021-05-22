<?php

namespace OriginEngine\Helpers\WorkingDirectory;

class ConfigDirectoryLocator
{

    public static function locate(): string
    {
        return $_SERVER['HOME'] . '/.atlas-cli';
    }

}
