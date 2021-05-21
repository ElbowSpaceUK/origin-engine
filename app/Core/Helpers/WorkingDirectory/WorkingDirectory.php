<?php

namespace App\Core\Helpers\WorkingDirectory;

use App\Core\Site\Site;

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

    public static function fromInstanceId(string $instanceId): WorkingDirectory
    {
        return new WorkingDirectory(
            InstanceDirectoryLocator::fromInstanceId($instanceId)
        );
    }

    public static function fromSite(Site $site): WorkingDirectory
    {
        return static::fromInstanceId($site->getInstanceId());
    }

    public static function fromPath(string $path): WorkingDirectory
    {
        return new WorkingDirectory($path);
    }

}
