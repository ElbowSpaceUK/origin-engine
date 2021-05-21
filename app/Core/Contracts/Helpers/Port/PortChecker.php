<?php

namespace App\Core\Contracts\Helpers\Port;

interface PortChecker
{

    public static function isFree(int $port): bool;

    public static function isTaken(int $port): bool;


}
