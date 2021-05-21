<?php

namespace App\Core\Helpers\Env;

use Dotenv\Dotenv;

class EnvFactory
{

    public static function fromDotEnv(Dotenv $dotEnv): Env
    {
        return static::fromArray(
            $dotEnv->load()
        );
    }

    public static function fromArray(array $env): Env
    {
        $envModel = new Env();
        foreach($env as $name => $value) {
            $envModel->addVariable($name, $value);
        }
        return $envModel;
    }

}
