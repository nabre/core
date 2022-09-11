<?php

namespace Nabre\Forms\Admin;

use Collective\Html\HtmlFacade as Html;
use Nabre\Models\SettingGroup;
use Nabre\Repositories\Form\Structure;

class SettingsForm extends Structure
{
    function build()
    {
        SettingGroup::with(['settings'])->whereHas('settings', function ($q) {
            $q->whereDoesntHave('user');
        })->get()->sortBy('name')->each(function ($group) {
            $this->addHtml(Html::tag('h2',$group->string));
            $group->settings()->whereDoesntHave('user')->get()->sortBy(config('setting.database.key'))->each(function ($i) {
                $this->add($i[config('setting.database.key')], $i->type_load, $i['string'], 'fake')->setFakeValue($i->value)->info($i->description, 'light');
            });
        });
    }
}
