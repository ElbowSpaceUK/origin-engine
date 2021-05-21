<?php

namespace App\Core\Setup;

use Illuminate\Contracts\Config\Repository;

class SetupManager
{

    /**
     * @var Repository
     */
    private Repository $config;

    public function __construct(Repository $config)
    {

        $this->config = $config;
    }

    public function setup()
    {
        foreach($this->config->get('app.setup.steps') as $step) {
            $class = app($step);
            if(!$class->isSetup()) {
                $class->run();
            }
        }
    }

}
