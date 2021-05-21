<?php

namespace App\Core\Helpers\Storage;

use App\Core\Helpers\Settings\Settings;
use App\Core\Helpers\WorkingDirectory\ConfigDirectoryLocator;
use Illuminate\Support\Str;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

class Filesystem
{

    // TODO Decorator around filesystem to append on the working directory, so we can just work in the working directory.

    public static function create(): SymfonyFilesystem
    {
        return new SymfonyFilesystem();
    }

    public static function database(string $append = ''): string
    {
        return static::append(
            ConfigDirectoryLocator::locate(),
            $append
        );
    }

    public static function project(string $append = ''): string
    {
        if(!Settings::has('project-directory')) {
            throw new \Exception('Please set a project directory');
        }
        return static::append(
            Settings::get('project-directory'),
            $append
        );
    }

    public static function append(...$paths)
    {
        if(count($paths) === 0) {
            throw new \Exception('No paths given');
        }
        $finalPath = array_shift($paths);
        $finalPath = Str::endsWith($finalPath, DIRECTORY_SEPARATOR) ? Str::substr($finalPath, 0, -1) : $finalPath;
        foreach($paths as $path) {
            $path = Str::endsWith($path, DIRECTORY_SEPARATOR) ? Str::substr($path, 0, -1) : $path;
            $finalPath .= Str::startsWith($path, DIRECTORY_SEPARATOR)
                ? $path
                : DIRECTORY_SEPARATOR . $path;
        }
        return $finalPath;
    }

    public static function read(string $path)
    {
        return file_get_contents($path);
    }

}
