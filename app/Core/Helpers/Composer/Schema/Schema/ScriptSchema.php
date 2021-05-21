<?php


namespace App\Core\Helpers\Composer\Schema\Schema;


use Illuminate\Contracts\Support\Arrayable;

class ScriptSchema implements Arrayable
{

    private string $name;

    /**
     * @var array|string
     */
    private $commands;

    /**
     * ScriptSchema constructor.
     * @param string $name
     * @param array|string $commands
     */
    public function __construct(string $name, $commands)
    {
        $this->name = $name;
        $this->commands = $commands;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return array|string
     */
    public function getCommands()
    {
        return $this->commands;
    }

    /**
     * @param array|string $commands
     */
    public function setCommands($commands): void
    {
        $this->commands = $commands;
    }

    public function toArray()
    {
        return [
            'name' => $this->name,
            'commands' => $this->commands
        ];
    }

}
