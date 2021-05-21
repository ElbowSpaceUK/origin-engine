<?php

namespace App\Core\Pipeline;

use App\Core\Helpers\IO\IO;
use App\Core\Helpers\WorkingDirectory\WorkingDirectory;
use Illuminate\Support\Str;

abstract class Pipeline implements \App\Core\Contracts\Pipeline\Pipeline
{

    protected string $upStartText = 'Starting installation.';

    protected string $upText = 'installing...';

    protected string $upFinishText = 'Installed.';

    protected string $upFailedText = 'Installation failed, aborting.';


    protected string $downStartText = 'Starting uninstall.';

    protected string $downText = 'uninstalling...';

    protected string $downFinishText = 'Uninstalled.';

    protected string $downFailedText = 'Uninstall failed.';


    public function install(WorkingDirectory $directory)
    {
        $completedTasks = [];
        IO::info($this->upStartText);
        try {
            foreach($this->getTasks() as $task) {
                $this->installTask($directory, $task);
                $completedTasks[] = $task;
            }
            IO::success($this->upFinishText);
        } catch (\Exception $e) {
            IO::error($this->upFailedText);

            foreach(array_reverse($completedTasks) as $task) {
                $this->uninstallTask($directory, $task);
            }

            IO::info($this->downFinishText);

            throw $e;
        }
    }

    public function uninstall(WorkingDirectory $directory)
    {
        $completedTasks = [];
        IO::info($this->downStartText);
        try {
            foreach(array_reverse($this->getTasks()) as $task) {
                $this->uninstallTask($directory, $task);
                $completedTasks[] = $task;
            }
            IO::success($this->downFinishText);
        } catch (\Exception $e) {
            IO::error($this->downFailedText);

            foreach(array_reverse($completedTasks) as $task) {
                $this->installTask($directory, $task);
            }

            IO::info($this->upFinishText);

            throw $e;
        }

    }

    public function installTask(WorkingDirectory $directory, ProvisionedTask $task)
    {
        IO::task($task->name(), fn() => $task->up($directory), $this->upText);
        return $task;
    }

    public function uninstallTask(WorkingDirectory $directory, ProvisionedTask $task)
    {
        IO::task($task->name(), fn() => $task->down($directory), $this->downText);
        return $task;
    }

    /**
     * @param WorkingDirectory $directory
     * @return array|ProvisionedTask[]
     */
    abstract protected function getTasks(): array;

    private function taskName(string $task): string
    {
        $reflectionClass = new \ReflectionClass($task);
        return Str::title(
            Str::kebab(
                $reflectionClass->getShortName()
            )
        );
    }
}
