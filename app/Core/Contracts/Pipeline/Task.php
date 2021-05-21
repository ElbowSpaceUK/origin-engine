<?php

namespace App\Core\Contracts\Pipeline;

use App\Core\Helpers\WorkingDirectory\WorkingDirectory;
use App\Core\Pipeline\ProvisionedTask;
use App\Core\Pipeline\TaskConfig;

/**
 * @method static ProvisionedTask provision() Provision the task to be used in a pipeline
 */
abstract class Task
{

    /**
     * @var TaskConfig
     */
    protected TaskConfig $config;

    public function __construct(TaskConfig $config)
    {
        $this->config = $config;
    }

    abstract public function up(WorkingDirectory $workingDirectory): void;

    abstract public function down(WorkingDirectory $workingDirectory): void;

    public static function __callStatic($name, $arguments)
    {
        if($name === 'provision') {
            if(empty($arguments)) {
                return ProvisionedTask::provision(static::class);
            }

            throw new \Exception(
                sprintf('Define a provision function on the build task at [%s]', static::class)
            );
        }
        throw new \BadMethodCallException(sprintf(
        'Call to undefined method %s::%s()', static::class, $name
    ));
    }

}
