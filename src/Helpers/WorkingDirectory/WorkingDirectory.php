<?php

namespace OriginEngine\Helpers\WorkingDirectory;

use OriginEngine\Site\Site;

class WorkingDirectory
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

    public static function fromDirectory(string $directory): WorkingDirectory
    {
        return new WorkingDirectory(
            ProjectDirectoryLocator::fromDirectory($directory)
        );
    }

    public static function fromSite(Site $site): WorkingDirectory
    {
        return static::fromDirectory($site->getDirectory());
    }

    public static function fromPath(string $path): WorkingDirectory
    {
        return new WorkingDirectory($path);
    }

}
