<?php

namespace OriginEngine\Helpers\Directory;

use Illuminate\Support\Str;
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

    public function getPathBasename(): string
    {
        return basename($this->path());
    }

    /**
     * Get the full path without the final directory
     *
     * @return string
     */
    public function getPathWithoutBasename(): string
    {
        return Str::substr($this->path(), 0, -strlen($this->getPathBasename()));
    }

    /**
     * @deprecated
     * @param Site $site
     * @return Directory
     */
    public static function fromSite(Site $site): Directory
    {
        return $site->getDirectory();
    }

    public static function fromFullPath(string $path): Directory
    {
        return new Directory($path);
    }

}
