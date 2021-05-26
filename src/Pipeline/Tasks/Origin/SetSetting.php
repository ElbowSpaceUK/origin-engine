<?php

namespace OriginEngine\Pipeline\Tasks\Origin;

use Illuminate\Support\Collection;
use \OriginEngine\Contracts\Helpers\Settings\SettingRepository;
use OriginEngine\Contracts\Setup\SetupStep;
use OriginEngine\Helpers\Directory\Directory;
use OriginEngine\Helpers\IO\Proxy;
use Illuminate\Support\Str;
use OriginEngine\Pipeline\Task;
use OriginEngine\Pipeline\TaskResponse;

class SetSetting extends Task
{
    /**
     * @param string $name Name of the setting to set
     * @param mixed $value Value of the setting to set
     */
    public function __construct(string $name, $value)
    {
        parent::__construct([
            'name' => $name,
            'value' => $value
        ]);
    }

    protected function execute(Directory $workingDirectory, Collection $config): TaskResponse
    {
        $oldValue = app(SettingRepository::class)->get($config->get('name'));
        $this->export('old-value', $oldValue);

        $this->writeDebug(
            sprintf('Setting %s to %s', $config->get('name'), $config->get('value'))
        );
        app(SettingRepository::class)->set($config->get('name'), $config->get('value'));

        return $this->succeeded();
    }

    protected function undo(Directory $workingDirectory, bool $status, Collection $config, Collection $output): void
    {
        app(SettingRepository::class)->set($config->get('name'), $output->get('old-value'));
    }

    protected function upName(Collection $config): string
    {
        return sprintf('Setting the %s value', $config->get('name'));
    }

    protected function downName(Collection $config): string
    {
        return sprintf('Reverting the %s value', $config->get('name'));
    }

}
