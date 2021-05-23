<?php

namespace OriginEngine\Pipeline;

class PipelineConfig
{

    /**
     * Configuration for the task
     *
     * @var array
     */
    private array $config = [];

    private array $aliasedConfig = [];

    public function __construct(array $config = [])
    {
        foreach($config as $key => $value) {
            if(str_contains($key, '.')) {
                $this->addWithKeyInConfigName($key, $value);
            } else {
                $this->aliasedConfig[$key] = $value;
            }
        }
    }

    public function addWithKeyInConfigName(string $name, $value)
    {
        if(str_contains($name, '.')) {
            $configName = explode('.', $name);
            $task = array_shift($configName);
            $this->add($task, implode('.', $configName) ,$value);
        } else {
            throw new \Exception(sprintf('Name [%s] did not contain a dot to split at', $name));
        }
    }

    public function add(string $task, string $name, $value)
    {
        if(!array_key_exists($task, $this->config)) {
            $this->config[$task] = [];
        }
        $this->config[$task][$name] = $value;
    }

    public function remove(string $task, string $name)
    {
        if(!array_key_exists($task, $this->config)) {
            $this->config[$task] = [];
        }
        if(array_key_exists($name, $this->config[$task])) {
            unset($this->config[$task][$name]);
        }
    }

    public function get(string $task, string $name, $default = null)
    {
        if(!array_key_exists($task, $this->config)) {
            $this->config[$task] = [];
        }
        if(array_key_exists($name, $this->config)) {
            return $this->config[$name];
        }
        return $default;
    }

    public function getAliasedConfiguration(): array
    {
        return $this->aliasedConfig;
    }

    public function getAll(string $task): array
    {
        if(!array_key_exists($task, $this->config)) {
            $this->config[$task] = [];
        }
        return $this->config[$task];
    }

    public function has(string $task, string $name): bool
    {
        if(!array_key_exists($task, $this->config)) {
            $this->config[$task] = [];
        }
        return array_key_exists($name, $this->config[$task]);
    }

}
