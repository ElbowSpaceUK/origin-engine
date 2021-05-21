<?php


namespace App\Core\Helpers\Composer\Operations\Operations;


use App\Core\Contracts\Helpers\Composer\Operation;
use App\Core\Helpers\Composer\Schema\Schema\ComposerSchema;
use App\Core\Helpers\Composer\Schema\Schema\PackageRepositorySchema;

class RemoveRepository implements Operation
{

    private string $type;
    private string $url;
    private array $options;
    /**
     * @var PackageRepositorySchema|null
     */
    private ?PackageRepositorySchema $package;

    public function __construct(string $type, string $url, array $options = [], ?PackageRepositorySchema $package = null)
    {
        $this->type = $type;
        $this->url = $url;
        $this->options = $options;
        $this->package = $package;
    }

    public function perform(ComposerSchema $composerSchema): ComposerSchema
    {
        $updatedRepositories = [];
        $found = false;

        $repositories = $composerSchema->getRepositories();
        foreach($repositories as $repository) {
            if(
                $repository->getType() === $this->type
                && $repository->getUrl() === $this->url
                && $repository->getOptions() === $this->options
                && ($repository->getPackage() ? $repository->getPackage()->toArray() === $this->package->toArray() : true)
            ){
                $found = true;
                continue;
            }
            $updatedRepositories[] = $repository;
        }

        if($found === false) {
            throw new \Exception(
                sprintf('Repository [%s : %s] was not required as a dependency', $this->type, $this->url)
            );
        }

        $composerSchema->setRepositories($updatedRepositories);
        return $composerSchema;
    }
}
