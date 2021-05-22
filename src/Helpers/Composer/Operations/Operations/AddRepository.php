<?php


namespace OriginEngine\Helpers\Composer\Operations\Operations;


use OriginEngine\Contracts\Helpers\Composer\Operation;
use OriginEngine\Helpers\Composer\Schema\Schema\ComposerSchema;
use OriginEngine\Helpers\Composer\Schema\Schema\PackageRepositorySchema;
use OriginEngine\Helpers\Composer\Schema\Schema\RepositorySchema;

class AddRepository implements Operation
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
        $repository = new RepositorySchema(
            $this->type,
            $this->url,
            $this->options,
            $this->package
        );

        $repositories = $composerSchema->getRepositories();
        array_unshift($repositories, $repository);

        $composerSchema->setRepositories($repositories);
        return $composerSchema;
    }
}
