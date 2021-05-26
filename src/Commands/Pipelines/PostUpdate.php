<?php

namespace OriginEngine\Commands\Pipelines;

use Illuminate\Support\Str;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Settings\SettingRepository;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Pipeline\Pipeline;
use OriginEngine\Pipeline\PipelineConfig;
use OriginEngine\Pipeline\PipelineHistory;
use OriginEngine\Pipeline\Tasks\Files\CreateDirectory;
use OriginEngine\Pipeline\Tasks\Files\CreateEmptyFile;
use OriginEngine\Pipeline\Tasks\Origin\MigrateLocalDatabase;
use OriginEngine\Pipeline\Tasks\Origin\SetSetting;

class PostUpdate extends Pipeline
{

    public function __construct()
    {
        $this->before('set-project-directory', function(PipelineConfig $config, PipelineHistory $history) {
            $currentValue = app(SettingRepository::class)->get('project-directory');

            if(empty($config->get('set-project-directory', 'value', null)) && empty($currentValue)) {
                $directory = IO::ask(
                    'What do you want the project directory to be?',
                    app(SettingRepository::class)->get('project-directory'),
                    fn($directory) => $this->validateDirectory($directory)
                );
                $config->add('set-project-directory', 'value', $directory);
            } else {
                return false;
            }
        });
    }

    public function getTasks(): array
    {
        return [
            'create-database-directory' => new CreateDirectory(Filesystem::database(), false),
            'create-database' => new CreateEmptyFile(Filesystem::database('atlas-cli.sqlite'), false),
            'migrate-database' => new MigrateLocalDatabase(),
            'set-project-directory' => new SetSetting('project-directory', null)
        ];
    }

    /**
     * Validate a project directory
     *
     * @param $directory
     * @return string
     */
    private function validateDirectory($directory): string
    {
        if(Str::contains($directory, '~')) {
            throw new \RuntimeException('Cannot locate your home directory ~. Please enter a relative or absolute path.');
        }
        if($directory === null) {
            throw new \RuntimeException('Please enter a directory');
        }

        return $directory;
    }
}
