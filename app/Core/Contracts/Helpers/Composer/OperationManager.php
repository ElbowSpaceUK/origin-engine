<?php


namespace App\Core\Contracts\Helpers\Composer;


interface OperationManager
{

    /**
     * Get an operation
     *
     * @param string $operation The name of the operator
     * @param array $parameters Parameters to create the operator with
     *
     * @return Operation
     */
    public function operation(string $operation, array $parameters = []): Operation;


}
