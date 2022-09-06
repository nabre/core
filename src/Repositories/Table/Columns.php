<?php

namespace Nabre\Repositories\Table;


use Collective\Html\HtmlFacade as Html;
use Collective\Html\FormFacade as Form;

class Columns
{
    static function cast($cast, $value = null)
    {
        switch ($cast) {
            case "boolean":
                if ($value) {
                    $color = 'text-success';
                    $icon = '<i class="fa-regular fa-circle-check"></i>';
                } else {
                    $color = 'text-danger';
                    $icon = '<i class="fa-regular fa-circle-xmark"></i>';
                }
                return Html::div($icon, ['class' => $color]);
                break;
            default:
                return $value;
                break;
        }
    }
}
