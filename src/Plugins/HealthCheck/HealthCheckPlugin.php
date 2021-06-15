<?php

namespace OriginEngine\Plugins\HealthCheck;

use OriginEngine\Foundation\Plugin;
use OriginEngine\Plugins\HealthCheck\Checkers\SiteFileIntegrityChecker;

class HealthCheckPlugin extends Plugin
{

    protected array $commands = [
        HealthCheckCommand::class,
        HealthCheckFixCommand::class,
    ];

    public function register()
    {
        $this->app->tag([SiteFileIntegrityChecker::class], ['healthcheck']);
        parent::register();
    }

}
