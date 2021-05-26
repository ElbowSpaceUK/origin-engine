<?php

namespace OriginEngine\Foundation;

use Illuminate\Support\ServiceProvider;

class CliServiceProvider extends ServiceProvider
{

    public function registerPlugin(string $plugin)
    {
        if(!is_a($plugin, Plugin::class, true)) {
            throw new \Exception(sprintf('The plugin [%s] does not extend the base Plugin class', $plugin));
        }
        $this->app->register($plugin);
    }

}
