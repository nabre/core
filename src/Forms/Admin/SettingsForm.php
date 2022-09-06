<?php

namespace Nabre\Forms\Admin;

use Nabre\Models\Setting;
use Nabre\Repositories\Form\Structure;

class SettingsForm extends Structure
{
    function build()
    {
        Setting::whereDoesntHave('user')->get()->each(function ($i) {
            $this->add($i[config('setting.database.key')], $i->type_load, $i['string'], 'fake')->setFakeValue($i->value)->info($i->description,'light');
        });
    }
}
