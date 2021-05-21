<?php


namespace App\Core\Helpers\Composer\Operations;


use App\Core\Contracts\Helpers\Composer\Operation;
use App\Core\Contracts\Helpers\Composer\OperationManager as OperationManagerContract;
use Closure;
use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;
use Illuminate\Support\Str;

class OperationManager implements OperationManagerContract
{

    /**
     * The container instance.
     *
     * @var Container
     */
    protected Container $container;

    /**
     * The registered custom operation creators.
     *
     * @var array
     */
    protected array $customCreators = [];

    /**
     * The array of created "operations".
     *
     * @var array
     */
    protected array $operations = [];

    /**
     * Create a new manager instance.
     *
     * @param Container $container
     * @return void
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Get a operation instance.
     *
     * @param string $operation
     * @param array $parameters
     * @return mixed
     *
     */
    public function operation(string $operation, array $parameters = []): Operation
    {
        // If the given operation has not been created before, we will create the instances
        // here and cache it so we can return it next time very quickly. If there is
        // already a operation created by this name, we'll just return that instance.
        if (! isset($this->operations[$operation])) {
            $this->operations[$operation] = $this->createOperation($operation, $parameters);
        }

        return $this->operations[$operation];
    }

    /**
     * Create a new operation instance.
     *
     * @param string $operation
     * @param array $parameters
     * @return mixed
     *
     */
    protected function createOperation(string $operation, array $parameters)
    {
        // First, we will determine if a custom operation creator exists for the given operation and
        // if it does not we will check for a creator method for the operation. Custom creator
        // callbacks allow developers to build their own "operations" easily using Closures.
        if (isset($this->customCreators[$operation])) {
            return $this->callCustomCreator($operation, $parameters);
        } else {
            $method = 'create'.Str::studly($operation).'Operation';

            if (method_exists($this, $method)) {
                return $this->$method($parameters);
            }
        }

        throw new InvalidArgumentException("Operation [$operation] not supported.");
    }

    /**
     * Call a custom operation creator.
     *
     * @param string $operation
     * @param array $parameters
     * @return mixed
     */
    protected function callCustomCreator(string $operation, array $parameters)
    {
        return $this->customCreators[$operation]($parameters, $this->container);
    }

    /**
     * Register a custom operation creator Closure.
     *
     * The closure should take two arguments, parameters for the construct and the container
     *
     * @param  string  $operation
     * @param  \Closure  $callback
     * @return $this
     */
    public function extend($operation, Closure $callback)
    {
        $this->customCreators[$operation] = $callback;

        return $this;
    }

}
