<?php

namespace App\Core\Contracts\Helpers\Settings;

interface SettingRepository
{

    public function set(string $key, $value): void;

    public function get(string $key, $default = null);

    public function has(string $key): bool;

    public function forget(string $key): void;

}
