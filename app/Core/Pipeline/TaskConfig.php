<?php

namespace App\Core\Pipeline;

class TaskConfig
{
    /**
     * Configuration for the task
     *
     * @var array
     */
    private array $config;

    public static function parse($config): TaskConfig
    {
        if($config instanceof TaskConfig) {
            return $config;
        }
        if(is_array($config)) {
            return new static($config);
        }
        throw new \Exception(
            sprintf('Config must be an array or TaskConfig object, %s given', gettype($config))
        );
    }

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function add(string $name, $value)
    {
        $this->config[$name] = $value;
    }

    public function remove(string $name)
    {
        if(array_key_exists($name, $this->config)) {
            unset($this->config[$name]);
        }
    }

    public function get(string $name, $default = null)
    {
        if(array_key_exists($name, $this->config)) {
            return $this->config[$name];
        }
        return $default;
    }

    public function getAll(): array
    {
        return $this->config;
    }

}
