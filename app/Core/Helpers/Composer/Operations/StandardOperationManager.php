<?php

namespace App\Core\Helpers\Composer\Operations;

use App\Core\Helpers\Composer\Operations\Operations\AddRepository;
use App\Core\Helpers\Composer\Operations\Operations\ChangeDependencyVersion;
use App\Core\Helpers\Composer\Operations\Operations\Remove;
use App\Core\Helpers\Composer\Operations\Operations\RemoveRepository;
use App\Core\Helpers\Composer\Operations\Operations\RequireDev;
use App\Core\Helpers\Composer\Operations\Operations\RequirePackage;
use Illuminate\Support\Facades\Validator;

class StandardOperationManager extends OperationManager
{

    public function validateParameters(array $parameters, array $required, array $optional): void
    {
        $keys = array_keys($parameters);

        foreach($required as $key) {
            if(in_array($key, $keys)) {
                unset($keys[array_search($key, $keys)]);
            } else {
                throw new \Exception(
                    sprintf('Key %s is required but was not given', $key)
                );
            }
        }

        foreach($optional as $key) {
            if(in_array($key, $keys)) {
                unset($keys[array_search($key, $keys)]);
            }
        }

        if(count($keys) > 0) {
            throw new \Exception(
                sprintf('Keys [%s] are not supported', implode(', ', $keys))
            );
        }
    }

    public function createAddRepositoryOperation(array $parameters): AddRepository
    {
        $this->validateParameters($parameters, ['type', 'url'], ['options', 'package']);
        return new AddRepository(
            $parameters['type'],
            $parameters['url'],
            data_get($parameters, 'options', []),
            data_get($parameters, 'package', null)
        );
    }

    public function createRemoveRepositoryOperation(array $parameters): RemoveRepository
    {
        $this->validateParameters($parameters, ['type', 'url'], ['options', 'package']);
        return new RemoveRepository(
            $parameters['type'],
            $parameters['url'],
            data_get($parameters, 'options', []),
            data_get($parameters, 'package')
        );
    }

    public function createChangeDependencyVersionOperation(array $parameters): ChangeDependencyVersion
    {
        $this->validateParameters($parameters, ['name', 'version'], []);
        return new ChangeDependencyVersion(
            $parameters['name'],
            $parameters['version']
        );
    }

    public function createRemoveOperation(array $parameters): Remove
    {
        $this->validateParameters($parameters, ['name'], []);
        return new Remove(
            $parameters['name'],
        );
    }

    public function createRequireDevOperation(array $parameters): RequireDev
    {
        $this->validateParameters($parameters, ['name', 'version'], []);
        return new RequireDev(
            $parameters['name'],
            $parameters['version']
        );
    }

    public function createRequireOperation(array $parameters): RequirePackage
    {
        $this->validateParameters($parameters, ['name', 'version'], []);
        return new RequirePackage(
            $parameters['name'],
            $parameters['version']
        );
    }

}
