<?php

namespace OriginEngine\Plugins\HealthCheck;

use OriginEngine\Foundation\Plugin;
use OriginEngine\Plugins\HealthCheck\Checkers\ActiveFeatureIsSet;
use OriginEngine\Plugins\HealthCheck\Checkers\SiteFileIntegrityChecker;

class HealthCheckPlugin extends Plugin
{

    protected array $commands = [
        \OriginEngine\Plugins\HealthCheck\Commands\HealthCheckCommand::class,
        \OriginEngine\Plugins\HealthCheck\Commands\HealthCheckFixCommand::class,
    ];

    protected array $checkers = [
        SiteFileIntegrityChecker::class,
        ActiveFeatureIsSet::class
    ];

    public function register()
    {
        $this->app->tag($this->checkers, ['healthcheck']);
        parent::register();
    }

}
