<?php

namespace OriginEngine\Foundation;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\ServiceProvider;

abstract class Plugin extends ServiceProvider
{

    protected array $commands = [];

    /**
     * Get an array of class names corresponding to the commands to register
     *
     * @return array
     */
    public function getCommands(): array
    {
        return $this->commands;
    }

    public function boot()
    {
        $this->app['config']->set('commands.add', array_merge($this->getCommands(), $this->app['config']->get('commands.add', [])));
    }
}
