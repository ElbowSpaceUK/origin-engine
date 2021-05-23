<?php


namespace OriginEngine\Contracts\Setup;


use OriginEngine\Helpers\IO\Proxy;

abstract class SetupStep
{

    /**
     * @var Proxy
     */
    protected Proxy $io;

    public function __construct(Proxy $io)
    {
        $this->io = $io;
    }

    abstract public function run();

    abstract public function isSetup(): bool;

}