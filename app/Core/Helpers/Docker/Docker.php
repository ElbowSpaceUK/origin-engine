<?php

namespace App\Core\Helpers\Docker;

use Illuminate\Support\Arr;

class Docker
{

    private array $options = [];

    private string $image = '';

    private string $command = '';

    public function addVolume(string $localDirectory, string $dockerDirectory)
    {
        $this->withOption(
            sprintf('-v %s:%s', $localDirectory, $dockerDirectory)
        );
    }

    public function setWorkingDirectory(string $dockerDirectory)
    {
        $this->withOption(
            sprintf('-w %s', $dockerDirectory)
        );
    }

    public function withOption(string $option)
    {
        $this->options[] = $option;
    }

    public function setEnvironmentVariable(string $variable, string $value)
    {
        $this->withOption(
            sprintf('-e %s=%s', $variable, $value)
        );
    }

    public function image(string $image)
    {
        $this->image = $image;
    }

    public function run(string $command)
    {
        $this->command = $command;
    }

    public function getCommand(): string
    {
        return implode(
            ' ',
            array_merge(
                ['docker run --rm'],
                $this->options,
                [$this->image],
                [sprintf('%s', $this->command)]
            )
        );
    }

    public function __toString(): string
    {
        return $this->getCommand();
    }

}
