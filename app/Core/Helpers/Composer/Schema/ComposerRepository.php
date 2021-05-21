<?php

namespace App\Core\Helpers\Composer\Schema;

use App\Core\Helpers\Composer\Schema\Schema\ComposerSchema;
use App\Core\Helpers\WorkingDirectory\WorkingDirectory;

class ComposerRepository
{

    /**
     * @var ComposerFilesystem
     */
    private ComposerFilesystem $composerFilesystem;
    /**
     * @var ComposerSchemaFactory
     */
    private ComposerSchemaFactory $composerSchemaFactory;

    public function __construct(ComposerFilesystem $composerFilesystem, ComposerSchemaFactory $composerSchemaFactory)
    {
        $this->composerFilesystem = $composerFilesystem;
        $this->composerSchemaFactory = $composerSchemaFactory;
    }

    public function get(WorkingDirectory $workingDirectory, string $filename = 'composer.json'): ComposerSchema
    {
        return $this->composerSchemaFactory->create(
            $this->composerFilesystem->retrieve($workingDirectory, $filename)
        );
    }

    public function save(WorkingDirectory $workingDirectory, ComposerSchema $composerSchema, string $filename = 'composer.json')
    {
        $composer = $composerSchema->toArray();

        $this->composerFilesystem->put($workingDirectory, $composer, $filename);
    }

}
