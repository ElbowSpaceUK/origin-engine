<?php

namespace OriginEngine\Pipeline;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use OriginEngine\Command\SignalReceived;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Helpers\Storage\Filesystem;
use Spatie\SignalAwareCommand\Facades\Signal;

abstract class Task
{
    use CreatesTaskResponse;

    private static ?int $signal = null;

    private array $defaultConfiguration;

    private string $upName;

    private string $downName;

    private string $relativeDirectory;

    public function __construct(array $defaultConfiguration = [])
    {
        $this->defaultConfiguration = $defaultConfiguration;
    }

    public function run(Directory $workingDirectory, Collection $config): TaskResponse
    {
        $this->registerSignalHandlers();
        static::$signal = null;
        try {
            $response = $this->execute(
                $this->getWorkingDirectory($workingDirectory),
                $config
            );
            if(static::$signal !== null) {
                $response->setSuccess(false);
            }
        } catch (\Exception $e) {
            $this->writeError(sprintf('[%s] at %s, line %u', $e->getMessage(), $e->getFile(), $e->getCode()));
            $this->writeDebug($e->getTraceAsString());
            return $this->failed();
        }
        $this->clearSignalHandlers();
        return $response;
    }

    public function reverse(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        try {
            $this->undo($this->getWorkingDirectory($workingDirectory), $status, $config, $output);
        } catch (\Exception $e) {
            IO::error($e->getMessage());
        }
    }

    public function getDefaultConfiguration(): array
    {
        return $this->defaultConfiguration;
    }

    abstract protected function execute(Directory $workingDirectory, Collection $config): TaskResponse;

    abstract protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void;

    abstract protected function upName(Collection $config): string;

    abstract protected function downName(Collection $config): string;

    public function setUpName(string $name)
    {
        $this->upName = $name;

        return $this;
    }

    public function setDownName(string $name)
    {
        $this->downName = $name;

        return $this;
    }

    public function getUpName(Collection $config): string
    {
        if(isset($this->upName)) {
            return $this->upName;
        }
        return $this->upName($config);
    }

    public function getDownName(Collection $config): string
    {
        if(isset($this->downName)) {
            return $this->downName;
        }
        return $this->downName($config);
    }

    public function inRelativeDirectory(string $path)
    {
        $this->relativeDirectory = $path;
    }

    private function getWorkingDirectory(Directory $workingDirectory): Directory
    {
        if(isset($this->relativeDirectory)) {
            return Directory::fromFullPath(
                Filesystem::append(
                    $workingDirectory->path(),
                    $this->relativeDirectory
                )
            );
        }
        return $workingDirectory;
    }

    private function registerSignalHandlers()
    {
        static::$signal = null;
        Event::listen(SignalReceived::class, function(SignalReceived $signalEvent) {
            static::$signal = $signalEvent->signal;
        });
    }

    private function clearSignalHandlers()
    {
        Event::forget(SignalReceived::class);
    }

}
