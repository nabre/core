<?php

namespace Nabre\Repositories\FormTwo;

use Illuminate\Validation\Rule as ValidationeRule;

trait RuleParse
{
    static $combined = [
        'between' => ['array', 'file', 'numeric', 'string'],
        'gt' => ['array', 'file', 'numeric', 'string'],
        'gte' => ['array', 'file', 'numeric', 'string'],
        'lt' => ['array', 'file', 'numeric', 'string'],
        'lte' => ['array', 'file', 'numeric', 'string'],
        'max' => ['array', 'file', 'numeric', 'string'],
        'min' => ['array', 'file', 'numeric', 'string'],
        'password' => ['letters', 'mixed', 'numbers', 'symbols', 'uncompromised'],
        'size'=> ['array', 'file', 'numeric', 'string'],
    ];

    static function combinedRule(){
        return array_keys(self::$combined);
    }

    static function subRule($fn){
        return self::$combined[$fn]??[];
    }

    static function allSubRule(){
        $merged=[];
        array_map(function($v)use(&$merged){
            $merged=array_merge($merged,$v);
        },self::$combined);

        sort($merged);
        return array_values(array_unique($merged));
    }

    function parseRule(&$fn, $attribute = null)
    {
        $params = null;
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
            $params = explode(',', $params);
            switch ($fn) {
                default:
                    $params = ['array' => implode(", ", $params)];
                    break;
            }
        }

        $params = (array)$params;
        $params['attribute'] = $attribute;

        return compact('fn', 'params');
    }
}
