<?php

namespace OriginEngine\Helpers\Composer;

use OriginEngine\Helpers\Docker\Docker;
use OriginEngine\Helpers\Terminal\Executor;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;

class ComposerRunner
{

    private WorkingDirectory $workingDirectory;

    public function __construct(WorkingDirectory $workingDirectory)
    {
        $this->workingDirectory = $workingDirectory;
    }

    public static function for(WorkingDirectory $workingDirectory): ComposerRunner
    {
        return new static($workingDirectory);
    }

    public function update()
    {
        $this->composer(
            sprintf(
                'update --working-dir %s --quiet --no-interaction --ansi',
                $this->workingDirectory->path()
            )
        );
    }

    public function install()
    {
        $this->composer(
            sprintf(
                'install --working-dir %s --quiet --no-interaction --ansi',
                $this->workingDirectory->path()
            )
        );
    }

    public function composer(string $command)
    {
        $docker = new Docker();
        $docker->addVolume($this->workingDirectory->path(), '/opt');

        $docker->addVolume('$SSH_AUTH_SOCK', '/ssh-auth.sock');
        $docker->setEnvironmentVariable('SSH_AUTH_SOCK', '/ssh-auth.sock');

        $docker->setEnvironmentVariable('GITHUB_KEYSCAN', '"$(ssh-keyscan github.com 2> /dev/null)"');

        $docker->setWorkingDirectory('/opt');

        $docker->image('laravelsail/php74-composer:latest');

        $docker->run(
            sprintf('echo $GITHUB_KEYSCAN >> ~/.ssh/known_hosts && composer %s', $command)
        );

        return Executor::cd($this->workingDirectory)
            ->execute($docker);
    }

}
