<?php


namespace App\Core\Helpers\Composer\Operations\Operations;


use App\Core\Contracts\Helpers\Composer\Operation;
use App\Core\Helpers\Composer\Schema\Schema\ComposerSchema;
use App\Core\Helpers\Composer\Schema\Schema\PackageRepositorySchema;
use App\Core\Helpers\Composer\Schema\Schema\RepositorySchema;

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
