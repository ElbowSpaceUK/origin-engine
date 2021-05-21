<?php

namespace App\Core\Helpers\Composer\Operations;

use App\Core\Contracts\Helpers\Composer\Operation;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Operation operation(string $operation, array $parameters = [])) Get the operation registered as $operation, and pass it the $parameters
 */
class Operator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Core\Contracts\Helpers\Composer\OperationManager::class;
    }


}
