<?php

namespace Nabre\Observers;

use Nabre\Models\Setting as Model;

class SettingObserver
{
    function creating(Model $model)
    {
    }

    function saved(Model $model){
        $key=config('setting.database.key');
        if($model->$key=='app_locale'){
            session()->forget('locale');
        }
    }
}
