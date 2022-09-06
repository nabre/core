<?php

namespace Nabre\Forms\User;

use Nabre\Models\Setting;
use Nabre\Repositories\Form\Structure;

class SettingsForm extends Structure
{
    function build()
    {
        Setting::whereDoesntHave('user')->where('user_set',true)->get()->each(function($i){
            $this->add($i['key'],$i->type_load,$i['string'],'fake')->setFakeValue(setting($i['key']));
        });
    }
}
