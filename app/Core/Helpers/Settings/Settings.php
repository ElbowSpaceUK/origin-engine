<?php

namespace App\Core\Helpers\Settings;

use Illuminate\Support\Facades\Facade;

class Settings extends Facade
{

    protected static function getFacadeAccessor()
    {
        return \App\Core\Contracts\Helpers\Settings\SettingRepository::class;
    }

}
