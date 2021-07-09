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
            'update --working-dir /opt --no-interaction --ansi',
        );
    }

    public function composer(string $command)
    {
        // TODO make this extensible so its easy to override the way you run composer. Extract to ComposerExecutor class?
        $docker = new Docker();
        $docker->addVolume($this->workingDirectory->path(), '/opt');

        $docker->setEnvironmentVariable('SSH_KEY_PRIVATE', '"$(cat ~/.ssh/id_rsa)"');

        $docker->setWorkingDirectory('/opt');

        $docker->image(sprintf('laravelsail/php%s-composer:latest', $this->phpVersion));

        $docker->run(
            sprintf('mkdir -p ~/.ssh && printenv SSH_KEY_PRIVATE >> ~/.ssh/id_rsa && chmod 600 ~/.ssh/id_rsa && ssh-keyscan github.com >> ~/.ssh/known_hosts && composer %s', $command)
        );

        return Executor::cd($this->workingDirectory)
            ->execute($docker);
    }

    public function install(): string
    {
        return $this->composer(
            'install --working-dir /opt --no-interaction --ansi',
        );
    }

}
