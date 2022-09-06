<?php

namespace Nabre\Casts;

use Collective\Html\HtmlFacade as Html;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Nabre\Models\FormFieldType;

class SettingTypeCast implements CastsAttributes
{

    public function get($model, $key, $value, $attributes)
    {
        return optional(FormFieldType::where('key',$value)->first())->string??$value;
    }

    public function set($model, $key, $value, $attributes)
    {
        return [$key => $value];
    }
}
