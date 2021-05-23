<?php

namespace OriginEngine\Pipeline\Tasks;

use Illuminate\Support\Collection;
use OriginEngine\Pipeline\Task;
use OriginEngine\Helpers\Env\Env;
use OriginEngine\Helpers\Env\EnvRepository;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\IO\Proxy;
use OriginEngine\Helpers\Port\Port;
use OriginEngine\Helpers\WorkingDirectory\WorkingDirectory;
use OriginEngine\Pipeline\Old\ProvisionedTask;
use Illuminate\Contracts\Config\Repository;
use OriginEngine\Pipeline\TaskResponse;

class CheckPortsAreFree extends Task
{

    /**
     * Holds an array of ports used up by this task during execution
     *
     * @var array
     */
    private array $usedPorts = [];

    /**
     * @param string $environmentFile The name of the environment file where the port environment variables are saved
     * @param array $ports An array of variable names in the env file (as the key) and a human readable name (as the values)
     * @param bool $promptForPortOverride Should the user be asked to fill in a different port to use if in use.
     */
    public function __construct(string $environmentFile, array $ports = [], bool $promptForPortOverride = false)
    {
        parent::__construct([
            'environmentFile' => $environmentFile,
            'ports' => $ports,
            'promptForPortOverride' => $promptForPortOverride
        ]);
    }

    private function isPortTaken(int $port): bool
    {
        return in_array($port, $this->usedPorts)
            || Port::isTaken($port);
    }

    private function getNewPort(string $portName, int $port = null, bool $promptUser): int
    {
        $suggestedPort = $port + 1;
        while ($this->isPortTaken($suggestedPort)) {
            $suggestedPort++;
        }
        if ($promptUser) {
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

    protected function execute(WorkingDirectory $workingDirectory, Collection $config): TaskResponse
    {
        $envRepository = new EnvRepository($workingDirectory);
        $env = $envRepository->get($config->get('environmentFile'));

        $ports = $config->get('ports', []);
        $oldPorts = [];
        foreach ($ports as $variablePortName => $humanPortName) {
            $port = (int) $env->getVariable($variablePortName, null);

            $oldPorts[$variablePortName] = $port;

            while (!$port || $this->isPortTaken($port)) {
                $port = $this->getNewPort($humanPortName, $port, $config->get('promptForPortOverride'));
            }

            $env = $this->savePort($variablePortName, $port, $env);
        }
        $envRepository->update($env, $config->get('environmentFile'));
        return $this->succeeded([
            'old_ports' => $oldPorts
        ]);
    }

    protected function undo(WorkingDirectory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        $envRepository = new EnvRepository($workingDirectory);
        $env = $envRepository->get($config->get('environmentFile'));

        foreach ($output->get('old_ports') as $variablePortName => $oldValue) {
            $env->setVariable($variablePortName, $oldValue);
        }

        $envRepository->update($env, $config->get('environmentFile'));
    }

    protected function upName(Collection $config): string
    {
        return 'Checking ports are free';
    }

    protected function downName(Collection $config): string
    {
        return 'Reverting to previous port assignments';
    }
}
