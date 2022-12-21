<?php

namespace Nabre\Forms\User\Profile;

use Nabre\Repositories\Form\Structure;
use Nabre\Services\SettingService;

class SettingsForm extends Structure
{
    function build()
    {
        SettingService::user_setList()->get()->sortBy(config('setting.database.key'))->each(function($i){
            $this->add($i[config('setting.database.key')],$i->type_load,$i['string'],'fake')->setFakeValue(setting($i[config('setting.database.key')]));
        });
    }
}
