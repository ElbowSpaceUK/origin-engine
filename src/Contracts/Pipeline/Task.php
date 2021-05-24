<?php

namespace OriginEngine\Contracts\Pipeline;

use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Pipeline\ProvisionedTask;
use OriginEngine\Pipeline\PipelineConfig;

/**
 * @method static ProvisionedTask provision() Provision the task to be used in a pipeline
 */
abstract class Task
{

    /**
     * @var PipelineConfig
     */
    protected PipelineConfig $config;

    public function __construct(PipelineConfig $config)
    {
        $this->config = $config;
    }

    abstract public function up(Directory $workingDirectory): void;

    abstract public function down(Directory $workingDirectory): void;

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
