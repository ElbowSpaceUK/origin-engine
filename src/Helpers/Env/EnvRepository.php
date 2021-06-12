<?php

namespace OriginEngine\Helpers\Env;

use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Helpers\Directory\Directory;
use Illuminate\Support\Str;

class EnvRepository
{

    /**
     * @var Directory
     */
    private Directory $workingDirectory;

    public function __construct(Directory $workingDirectory)
    {
        $this->workingDirectory = $workingDirectory;
    }

    public function get(string $type = null): Env
    {
        $envRetriever = new EnvRetriever($this->workingDirectory->path());
        $env = $envRetriever->get($type ?? '.env');

        return EnvFactory::fromDotEnv($env);
    }

    public function update(Env $env, $type = null): void
    {
        $path = Filesystem::append($this->workingDirectory->path(), $type ?? '.env');

        $envFile = '';
        foreach($env->getVariables() as $name => $value) {
            $pattern = '%s=%s';
            if(Str::contains($value, ' ')) {
                $pattern = '%s="%s"';
            }
            $envFile .= sprintf($pattern, $name, $value) . PHP_EOL;
        }
        Filesystem::create()->remove($path);
        Filesystem::create()->appendToFile($path, $envFile);
    }

}
