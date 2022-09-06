<?php

namespace Nabre\Casts;

use Collective\Html\HtmlFacade as Html;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;



class FontawesomeCast implements CastsAttributes
{

    public function get($model, $key, $value, $attributes)
    {
        return is_null($value) ? null: Html::tag('i',null,['class'=>$value]);
    }

    public function set($model, $key, $value, $attributes)
    {
        return [$key => $value];
    }
}
