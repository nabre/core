<?php

namespace Nabre\Repositories\FormTwo;

use Illuminate\Validation\Rule as ValidationeRule;

trait ParseRule
{
    function parseRule(&$fn,$attribute=null)
    {
        $params=null;
        collect(explode(":", $fn))->each(function ($str, $pos) use (&$fn, &$params) {
            switch ($pos) {
                case 0:
                    $params = null;
                    $fn = $str;
                    break;
                case 1:
                    $params = $str;
                    break;
            }
        });

        if (!is_null($params)) {
            $params=explode(',', $params);
            switch ($fn) {
                default:
                    $params = ['array' => $params];
                    break;
            }
        }

        $params=(array)$params;
        $params['attribute']=$attribute;

        return __('validation.'.$fn,$params);
    }
}
