<?php

namespace OriginEngine\Helpers\Directory;

class ConfigDirectoryLocator
{

    public static function locate(): string
    {
        return $_SERVER['HOME'] . '/.atlas-cli';
    }

}
