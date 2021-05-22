<?php

namespace OriginEngine\Helpers\Settings;

use Illuminate\Support\Facades\Facade;

class Settings extends Facade
{

    protected static function getFacadeAccessor()
    {
        return \OriginEngine\Contracts\Helpers\Settings\SettingRepository::class;
    }

}
