<?php

namespace OriginEngine\Helpers\Directory;

use OriginEngine\Site\Site;

class Directory
{
    private string $directory;

    public function __construct(string $directory)
    {
        $this->directory = $directory;
    }

    public function path(): string
    {
        return $this->directory;
    }

    public function set(string $directory)
    {
        $this->directory = $directory;
    }

    public static function fromDirectory(string $directory): Directory
    {
        return new Directory(
            ProjectDirectoryLocator::fromDirectory($directory)
        );
    }

    public static function fromSite(Site $site): Directory
    {
        return $site->getDirectory();
    }

    public static function fromFullPath(string $path): Directory
    {
        return new Directory($path);
    }

}
