<?php

namespace App\Core\Helpers\Env;

class Env
{

    private $variables = [];

    public function addVariable(string $name, string $value): void
    {
        $this->variables[$name] = $value;
    }

    public function updateVariable(string $name, string $value): void
    {
        $this->addVariable($name, $value);
    }

    public function setVariable(string $name, string $value): void
    {
        $this->addVariable($name, $value);
    }

    public function removeVariable(string $name): void
    {
        if($this->hasVariable($name)) {
            unset($this->variables[$name]);
        }
    }

    public function getVariable(string $name, $default = null)
    {
        if($this->hasVariable($name)) {
            return $this->variables[$name];
        }
        return $default;
    }

    public function hasVariable(string $name): bool
    {
        return array_key_exists($name, $this->variables);
    }

    public function getVariables(): array
    {
        return $this->variables;
    }
}
