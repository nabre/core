<?php

namespace Nabre\Forms\User\Profile;

use Nabre\Models\Setting;
use Nabre\Repositories\Form\Structure;

class SettingsForm extends Structure
{
    function build()
    {
        Setting::whereDoesntHave('user')->where('user_set',true)->get()->sortBy(config('setting.database.key'))->each(function($i){
            $this->add($i[config('setting.database.key')],$i->type_load,$i['string'],'fake')->setFakeValue(setting($i[config('setting.database.key')]));
        });
    }
}
