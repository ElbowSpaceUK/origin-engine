<?php

namespace App\Core\Helpers\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingModel extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'key', 'value'
    ];
}
