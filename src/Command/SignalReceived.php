<?php

namespace OriginEngine\Command;

class SignalReceived
{

    public int $signal;

    public function __construct(int $signal) {
        $this->signal = $signal;
    }

}
