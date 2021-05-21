<?php

namespace App\Core\Helpers\Composer\Schema;

use App\Core\Helpers\Storage\Filesystem;
use App\Core\Helpers\WorkingDirectory\WorkingDirectory;

class ComposerFilesystem
{

    public function retrieve(WorkingDirectory $directory, string $filename = 'composer.json'): array
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

    public function put(WorkingDirectory $directory, array $composer, string $filename = 'composer.json'): void
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
