<?php

namespace App\Core\Pipeline\Tasks;

use App\Core\Contracts\Pipeline\Task;
use App\Core\Helpers\Env\Env;
use App\Core\Helpers\Env\EnvRepository;
use App\Core\Helpers\IO\IO;
use App\Core\Helpers\IO\Proxy;
use App\Core\Helpers\Port\Port;
use App\Core\Helpers\WorkingDirectory\WorkingDirectory;
use App\Core\Pipeline\ProvisionedTask;
use Illuminate\Contracts\Config\Repository;

class ValidatePortEntries extends Task
{

    private array $usedPorts = [];

    public static function provision(string $environmentFile, array $portVariables = [], array $portHumanNames = [], bool $promptForPortOverride = false): ProvisionedTask
    {
        if (count($portVariables) !== count($portHumanNames)) {
            throw new \Exception('Mismatch in length of ports to check and port names.');
        }
        return ProvisionedTask::provision(static::class)
            ->dependencies([
                'environmentFile' => $environmentFile,
                'portVariables' => $portVariables,
                'portHumanNames' => $portHumanNames,
                'promptForPortOverride' => $promptForPortOverride
            ]);
    }

    public function up(\App\Core\Helpers\WorkingDirectory\WorkingDirectory $workingDirectory): void
    {
        $envRepository = new EnvRepository($workingDirectory);
        $env = $envRepository->get($this->config->get('environmentFile'));

        $portNames = $this->config->get('portHumanNames', []);
        foreach ($this->config->get('portVariables', []) as $index => $envName) {
            $portName = $portNames[$index];
            $port = (int)$env->getVariable($envName, null);

            while (!$port || $this->isPortTaken($port)) {
                $port = $this->getNewPort($portName, $port);
            }

            $env = $this->savePort($envName, $port, $env);
        }
        $envRepository->update($env, $this->config->get('environmentFile'));
    }

    private function isPortTaken(int $port): bool
    {
        return in_array($port, $this->usedPorts)
            || Port::isTaken($port);
    }

    private function getNewPort(string $portName, int $port = null): int
    {
        $suggestedPort = $port + 1;
        while ($this->isPortTaken($suggestedPort)) {
            $suggestedPort++;
        }
        if ($this->config->get('promptForPortOverride')) {
            return (int)IO::ask(
                sprintf('Port %s is in use, please choose a port for the %s', $port ?? '[no port]', $portName),
                $suggestedPort,
                fn($port) => $this->validateIsPort($port)
            );
        }
        return $suggestedPort;
    }

    private function validateIsPort($port): int
    {
        if (!$port || (int)$port <= 0) {
            throw new \Exception(sprintf('%s is not a valid port', $port));
        }
        return (int)$port;
    }

    private function savePort(string $envName, int $port, Env $env): Env
    {
        $this->usedPorts[] = $port;
        $env->setVariable($envName, $port);
        return $env;
    }

    public function down(\App\Core\Helpers\WorkingDirectory\WorkingDirectory $workingDirectory): void
    {
        // No down tasks
    }
}
