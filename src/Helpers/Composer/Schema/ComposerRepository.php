<?php

namespace OriginEngine\Helpers\Composer\Schema;

use OriginEngine\Helpers\Composer\Schema\Schema\ComposerSchema;
use OriginEngine\Helpers\Directory\Directory;

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

    public function get(Directory $workingDirectory, string $filename = 'composer.json'): ComposerSchema
    {
        return $this->composerSchemaFactory->create(
            $this->composerFilesystem->retrieve($workingDirectory, $filename)
        );
    }

    public function save(Directory $workingDirectory, ComposerSchema $composerSchema, string $filename = 'composer.json')
    {
        $composer = $composerSchema->toArray();

        $this->composerFilesystem->put($workingDirectory, $composer, $filename);
    }

}
