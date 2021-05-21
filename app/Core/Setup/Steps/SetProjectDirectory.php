<?php

namespace App\Core\Setup\Steps;

use \App\Core\Contracts\Helpers\Settings\SettingRepository;
use App\Core\Contracts\Setup\SetupStep;
use App\Core\Helpers\IO\Proxy;
use Illuminate\Support\Str;

class SetProjectDirectory extends SetupStep
{
    /**
     * @var SettingRepository
     */
    protected SettingRepository $settingRepository;

    public function __construct(Proxy $io, SettingRepository $settingRepository)
    {
        parent::__construct($io);
        $this->settingRepository = $settingRepository;
    }

    public function run()
    {
        $directory = $this->getDirectory();

        if(! is_dir($directory) && !mkdir($directory, 0777, true)) {
            throw new \Exception(sprintf('Could not create directory %s.', $directory));
        }

        if(($realpath = realpath($directory)) === false) {
            throw new \Exception(sprintf('Directory %s could not be loaded.', $realpath));
        }

        $this->settingRepository->set('project-directory', $realpath);

        $this->io->info(sprintf('Using project directory %s', $realpath));
    }

    private function getDirectory(): string
    {
        return $this->io->ask(
            'What do you want the project directory to be?',
            $this->settingRepository->get('project-directory'),
            fn($directory) => $this->validateDirectory($directory)
        );
    }

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

    public function isSetup(): bool
    {
        return $this->settingRepository->has('project-directory');
    }
}
