<?php


namespace App\Core\Helpers\Composer;


use App\Core\Contracts\Helpers\Composer\OperationManager as OperationManagerContract;
use App\Core\Helpers\Composer\Schema\ComposerRepository;
use App\Core\Helpers\Composer\Schema\Schema\PackageRepositorySchema;
use App\Core\Helpers\WorkingDirectory\WorkingDirectory;

class ComposerModifier
{

    private array $operations = [];

    /**
     * @var OperationManagerContract
     */
    private OperationManagerContract $operationManager;
    /**
     * @var WorkingDirectory
     */
    private WorkingDirectory $workingDirectory;
    private string $filename;
    /**
     * @var ComposerRepository
     */
    private ComposerRepository $composerRepository;


    public function __construct(WorkingDirectory $workingDirectory, OperationManagerContract $operationManager, ComposerRepository $composerRepository, string $filename = 'composer.json')
    {
        $this->operationManager = $operationManager;
        $this->workingDirectory = $workingDirectory;
        $this->filename = $filename;
        $this->composerRepository = $composerRepository;
    }

    public static function for(WorkingDirectory $workingDirectory, string $filename = 'composer.json'): ComposerModifier
    {
        return app(ComposerModifier::class, [
            'workingDirectory' => $workingDirectory,
            'filename' => $filename
        ]);
    }

    public function addOperation(string $operation, array $arguments): ComposerModifier
    {
        $this->operations[] = [
            'operation' => $operation,
            'arguments' => $arguments
        ];

        return $this;
    }

    public function transform(): void
    {
        $composerSchema = $this->composerRepository->get($this->workingDirectory, $this->filename);

        foreach($this->operations as $operationDetails) {
            $operation = $operationDetails['operation'];
            $arguments = $operationDetails['arguments'];

            $composerSchema = $this->operationManager->operation($operation, $arguments)
                ->perform($composerSchema);
        }

        $this->operations = [];

        $this->composerRepository->save($this->workingDirectory, $composerSchema, $this->filename);
    }

    public function require(string $name, string $version): ComposerModifier
    {
        return $this->addOperation('require', [
            'name' => $name, 'version' => $version
        ]);
    }

    public function addRepository(string $type, string $url, array $options = [], ?PackageRepositorySchema $packageRepositorySchema = null): ComposerModifier
    {
        return $this->addOperation('add-repository', [
            'type' => $type, 'url' => $url, 'options' => $options, 'package' => $packageRepositorySchema
        ]);
    }

    public function removeRepository(string $type, string $url, array $options = [], ?PackageRepositorySchema $packageRepositorySchema = null): ComposerModifier
    {
        return $this->addOperation('remove-repository', [
            'type' => $type, 'url' => $url, 'options' => $options, 'package' => $packageRepositorySchema
        ]);
    }

    public function changeDependencyVersion(string $name, string $version): ComposerModifier
    {
        return $this->addOperation('change-dependency-version', [
            'name' => $name, 'version' => $version
        ]);
    }

    public function remove(string $name): ComposerModifier
    {
        return $this->addOperation('remove', [
            'name' => $name
        ]);
    }

    public function requireDev(string $name, string $version): ComposerModifier
    {
        return $this->addOperation('require-dev', [
            'name' => $name, 'version' => $version
        ]);
    }

    public function __destruct()
    {
        if(count($this->operations) > 0) {
            $this->transform();
        }
    }

}
