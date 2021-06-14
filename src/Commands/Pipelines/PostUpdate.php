<?php

namespace OriginEngine\Commands\Pipelines;

use Illuminate\Support\Str;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Helpers\IO\IO;
use OriginEngine\Helpers\Settings\SettingRepository;
use OriginEngine\Helpers\Storage\Filesystem;
use OriginEngine\Helpers\Terminal\Executor;
use OriginEngine\Pipeline\Pipeline;
use OriginEngine\Pipeline\PipelineConfig;
use OriginEngine\Pipeline\PipelineHistory;
use OriginEngine\Pipeline\Tasks\Files\CreateDirectory;
use OriginEngine\Pipeline\Tasks\Files\CreateEmptyFile;
use OriginEngine\Pipeline\Tasks\Origin\MigrateLocalDatabase;
use OriginEngine\Pipeline\Tasks\Origin\SetSetting;

class PostUpdate extends Pipeline
{

    protected string $alias = 'post-update';

    public function __construct()
    {
        $this->before('set-project-directory', function (PipelineConfig $config, PipelineHistory $history) {
            $currentValue = app(SettingRepository::class)->get('project-directory');

            if (empty($config->get('set-project-directory', 'value', null)) && empty($currentValue)) {
                $directory = IO::ask(
                    'What do you want the project directory to be?',
                    app(SettingRepository::class)->get('project-directory'),
                    fn($directory) => $this->validateDirectory($directory)
                );
                if (Str::contains($directory, '~')) {
                    $home = Executor::cd(Directory::fromFullPath('~'))->execute('pwd');
                    $directory = Str::replace('~', $home, $directory);
                }
                $config->add('set-project-directory', 'value', $directory);
            } else {
                return false;
            }
        });
    }

    /**
     * Validate a project directory
     *
     * @param $directory
     * @return string
     */
    private function validateDirectory($directory): string
    {
        if ($directory === null) {
            throw new \RuntimeException('Please enter a directory');
        }

        return $directory;
    }

    public function tasks(): array
    {
        return [
            'create-database-directory' => new CreateDirectory(Filesystem::database(), false),
            'create-database' => new CreateEmptyFile(Filesystem::database(config()->get('database.name', 'origin') . '.sqlite'), false),
            'migrate-database' => new MigrateLocalDatabase(),
            'set-project-directory' => new SetSetting('project-directory', null)
        ];
    }

}
