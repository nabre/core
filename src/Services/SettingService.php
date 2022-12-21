<?php

namespace Nabre\Services;

use Nabre\Models\Setting;

class SettingService
{
    static function user_setList()
    {
        return Setting::whereDoesntHave('user')->where('user_set',true);
    }
}
