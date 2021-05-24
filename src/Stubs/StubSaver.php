<?php

namespace OriginEngine\Stubs;

use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Stubs\Entities\CompiledStub;

class StubSaver
{

    /**
     * @var Directory
     */
    private Directory $workingDirectory;

    private bool $force = false;

    public function __construct(Directory $workingDirectory)
    {
        $this->workingDirectory = $workingDirectory;
    }

    public function force(bool $force = true): StubSaver
    {
        $this->force = $force;
        return $this;
    }

    public function save(CompiledStub $stubFile, bool $dryRun = false)
    {
        // Get the path to save in
        if($stubFile->getStubFile()->getLocation() !== null) {
            $path = Filesystem::append($this->workingDirectory->path(), $stubFile->getStubFile()->getLocation(), $stubFile->getStubFile()->getFileName());
        } else {
            $path = Filesystem::append($this->workingDirectory->path(), $stubFile->getStubFile()->getFileName());
        }

        $fileAlreadyExists = Filesystem::create()->exists($path);

        if($dryRun) {
            IO::info(sprintf('File %s %s', $path, $fileAlreadyExists ? ' (already exists)' : ''));
            IO::writelns(explode('\n', $stubFile->getContent()));
            return;
        }

        if($fileAlreadyExists && !$this->force) {
            IO::warning(sprintf('Skipping file %s', $path));
            return;
        }


        // Make the directory
        $directory = dirname($path);
        if(!Filesystem::create()->exists($directory)) {
            Filesystem::create()->mkdir($directory);
        }

        // Save
        IO::task(sprintf('Save %s', $path), fn() => file_put_contents($path, $stubFile->getContent()), 'Saving...');
    }

    public static function in(Directory $workingDirectory): StubSaver
    {
        return new static($workingDirectory);
    }

}
