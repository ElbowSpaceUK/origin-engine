<?php

namespace OriginEngine\Helpers\Composer\Schema;

use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Helpers\Directory\Directory;

class ComposerFilesystem
{

    public function retrieve(Directory $directory, string $filename = 'composer.json'): array
    {
        $path = Filesystem::append(
            $directory->path(),
            $filename
        );

        if(Filesystem::create()->exists($path)) {
            return json_decode(
                Filesystem::read($path), true
            );
        }

        throw new \Exception(
            sprintf('Cannot find composer schema at path [%s].', $path)
        );
    }

    public function put(Directory $directory, array $composer, string $filename = 'composer.json'): void
    {
        $path = Filesystem::append(
            $directory->path(),
            $filename
        );

        if(Filesystem::create()->exists($path)) {
            Filesystem::create()->remove($path);
        }

        file_put_contents($path, json_encode($composer, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
    }

}
