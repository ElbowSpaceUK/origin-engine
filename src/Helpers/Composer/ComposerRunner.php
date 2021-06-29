<?php

namespace OriginEngine\Helpers\Composer;

use OriginEngine\Helpers\Docker\Docker;
use OriginEngine\Helpers\Terminal\Executor;
use OriginEngine\Helpers\Directory\Directory;

class ComposerRunner
{

    private Directory $workingDirectory;

    private string $phpVersion = '74';

    /**
     * @param Directory $workingDirectory
     */
    public function __construct(Directory $workingDirectory)
    {
        $this->workingDirectory = $workingDirectory;
    }

    public static function for(Directory $workingDirectory): ComposerRunner
    {
        return new static($workingDirectory);
    }

    /**
     * @param string $phpVersion One of '74' or '80'
     */
    public function withPhp(string $phpVersion = '74'): ComposerRunner
    {
        $this->phpVersion = $phpVersion;

        return $this;
    }

    public function update()
    {
        return $this->composer(
            'update --working-dir ' . $this->workingDirectory->path() . ' --no-interaction --ansi',
        );
    }

    public function composer(string $command)
    {
        // TODO make this extensible so its easy to override the way you run composer. Extract to ComposerExecutor class?
        $docker = new Docker();
        $docker->addVolume($this->workingDirectory->path(), '/opt');

        $docker->addVolume('$SSH_AUTH_SOCK', '/ssh-auth.sock');
        $docker->setEnvironmentVariable('SSH_AUTH_SOCK', '/ssh-auth.sock');

        $docker->setEnvironmentVariable('GITHUB_KEYSCAN', '"$(ssh-keyscan github.com 2> /dev/null)"');

        $docker->setWorkingDirectory('/opt');

        $docker->image(sprintf('laravelsail/php%s-composer:latest', $this->phpVersion));

        $docker->run(
            sprintf('pwd && composer %s', $command)
        );

        return Executor::cd($this->workingDirectory)
            ->execute($docker);
    }

    public function install(): string
    {
        return $this->composer(
            'install --working-dir ' . $this->workingDirectory->path() . ' --no-interaction --ansi',
        );
    }

}
