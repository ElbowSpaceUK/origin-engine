<?php

namespace App\Core\Helpers\Port;

class FSockOpenPortChecker implements \App\Core\Contracts\Helpers\Port\PortChecker
{

    public static function isFree(int $port): bool
    {
        try {
            set_error_handler(fn ($errorCode, $errorMessage) => null, E_WARNING);
            $fp = fsockopen('localhost', $port, $errorCode, $errorMessage, 2);
            restore_error_handler();
            if(!$fp) {
                return true;
            }
            return false;
        } catch (\Exception $e) {}
        return true;
    }

    public static function isTaken(int $port): bool
    {
        return !static::isFree($port);
    }

}
