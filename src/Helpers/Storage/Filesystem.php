<?php

namespace OriginEngine\Helpers\Storage;

use Illuminate\Support\Traits\ForwardsCalls;
use OriginEngine\Helpers\Settings\Settings;
use OriginEngine\Helpers\Directory\ConfigDirectoryLocator;
use Illuminate\Support\Str;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

class Filesystem extends SymfonyFilesystem
{
    use ForwardsCalls;

    /**
     * Get the path to the directory where the database is saved
     *
     * @param string $append Append this string to the database root path
     * @return string The path to the database directory, with the additional string if given
     *
     * @throws \Exception
     */
    public static function database(string $append = ''): string
    {
        return static::append(
            ConfigDirectoryLocator::locate(),
            $append
        );
    }

    /**
     * Get the path to the project directory, where sites are stored
     *
     * @param string $append Append this string to the project root path
     * @return string The path to the project directory, with the additional string if given
     *
     * @throws \Exception
     */
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

    /**
     * Append multiple paths together.
     *
     * @param string ...$paths Any number of paths to append.
     * @return string The paths in a coherent file structure
     * @throws \Exception
     */
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

    /**
     * Get the contents of a file
     *
     * @param string $path The path of the file to read
     * @return string The contents of the file
     *
     * @throws \Exception If the file could not be read
     */
    public function read(string $path): string
    {
        $fileContents = file_get_contents($path);
        if($fileContents === false) {
            throw new \Exception(sprintf('Could not read the given file [%s]', $path));
        }
        return $fileContents;
    }

    public static function create()
    {
        return new static();
    }

}
