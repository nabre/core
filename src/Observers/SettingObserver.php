<?php

namespace Nabre\Observers;

use Nabre\Models\Setting as Model;
use Nabre\Models\SettingGroup;

class SettingObserver
{
    function saved(Model $model)
    {
        $key = config('setting.database.key');
        if ($model->$key == 'app_locale') {
            session()->forget('locale');
        }

        if (is_null($model->user)) {
            $name = collect(explode("_", $model->$key))->first();
            $settingGroup = SettingGroup::firstOrCreate(compact('name'), compact('name'))->id;
            $model->recursiveSaveQuietly(compact('settingGroup'));
        }
    }
}
