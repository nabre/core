<?php

namespace Nabre\Repositories\FormTwo;

use Illuminate\Validation\Rule;

class Rules
{
    static function accepted()
    {
        return __FUNCTION__;
    }

    static function active_url()
    {
        return __FUNCTION__;
    }

    static function afterDate(string $date)
    {
        return 'after:' . $date;
    }

    static function afterOrEqual(string $date)
    {
        return 'after:' . $date;
    }

    static function alpha()
    {
        return __FUNCTION__;
    }

    static function alpha_dash()
    {
        return __FUNCTION__;
    }

    static function alpha_num()
    {
        return __FUNCTION__;
    }

    static function array(array $items, ?int $size)
    {
        return __FUNCTION__ . ":" . implode(',', $items) . !is_null($size) ? '|' . self::size($size) : '';
    }

    static function ascii()
    {
        return __FUNCTION__;
    }

    static function beforeDate(string $date)
    {
        return 'before:' . $date;
    }

    static function beforeOrEqual(string $date)
    {
        return 'after:' . $date;
    }

    static function between($min, $max)
    {
        return __FUNCTION__ . ":" . $min . "," . $max;
    }

    static function boolean()
    {
        return __FUNCTION__;
    }

    static function confirmed()
    {
        return __FUNCTION__;
    }

    static function date()
    {
        return __FUNCTION__;
    }

    static function date_equals(string $date)
    {
        return __FUNCTION__ . ":" . $date;
    }

    static function decimal($min, $max = null)
    {
        return __FUNCTION__ . ":" . $min . (!is_null($max)) ? "," . $max : '';
    }

    static function declined()
    {
        return __FUNCTION__;
    }

    static function different(string $field)
    {
        return __FUNCTION__ . ":" . $field;
    }

    static function digits(string $value)
    {
        return __FUNCTION__ . ":" . $value;
    }

    static function digits_between($min, $max)
    {
        return __FUNCTION__ . ":" . $min . "," . $max;
    }

    static function doesnt_end_with(array $values)
    {
        return __FUNCTION__ . ':' . implode(",", $values);
    }

    static function doesnt_start_with(array $values)
    {
        return __FUNCTION__ . ':' . implode(",", $values);
    }

    static function email(string $validator = 'rfc,dns')
    {
        return __FUNCTION__ . ':' . $validator;
    }

    static function ends_with(array $values)
    {
        return __FUNCTION__ . ':' . implode(",", $values);
    }

    static function exclude()
    {
        return __FUNCTION__;
    }

    static function exists(string $table, ?string $column = null)
    {
        return Rule::exists($table, $column);
    }

    static function file(?int $size)
    {
        return __FUNCTION__ . !is_null($size) ? '|' . self::size($size) : '';
    }

    static function filled()
    {
        return __FUNCTION__;
    }

    static function image()
    {
        return __FUNCTION__;
    }

    static function in(array $array)
    {
        return Rule::in($array);
    }

    static function integer(?int $size = null)
    {
        return __FUNCTION__ . !is_null($size) ? '|' . self::size($size) : '';
    }

    static function ip()
    {
        return __FUNCTION__;
    }

    static function ipv4()
    {
        return __FUNCTION__;
    }

    static function ipv6()
    {
        return __FUNCTION__;
    }

    static function json()
    {
        return __FUNCTION__;
    }

    static function lowercase()
    {
        return __FUNCTION__;
    }

    static function mac_address()
    {
        return __FUNCTION__;
    }

    static function mimetypes($array)
    {
        return __FUNCTION__ . ":" . implode(",", (array)$array);
    }

    static function mimes($array)
    {
        return __FUNCTION__ . ":" . implode(",", (array)$array);
    }

    static function min(int $value)
    {
        return __FUNCTION__ . ":" . $value;
    }

    static function min_digits(int $value)
    {
        return __FUNCTION__ . ":" . $value;
    }

    static function multiple_of(int $value)
    {
        return __FUNCTION__ . ":" . $value;
    }

    static function not_in(array $array)
    {
        return Rule::notIn($array);
    }

    static function not_regex(string $pattern)
    {
        return __FUNCTION__ . ":" . $pattern;
    }

    static function nullable()
    {
        return __FUNCTION__;
    }

    static function numeric()
    {
        return __FUNCTION__;
    }

    static function password()
    {
        return __FUNCTION__;
    }

    static function present()
    {
        return __FUNCTION__;
    }

    static function prohibited()
    {
        return __FUNCTION__;
    }

    static function regex(string $pattern)
    {
        return __FUNCTION__ . ":" . $pattern;
    }

    static function required()
    {
        return __FUNCTION__;
    }

    static function required_with($array)
    {
        return __FUNCTION__ . ":" . implode(",", (array)$array);
    }

    static function required_with_all($array)
    {
        return __FUNCTION__ . ":" . implode(",", (array)$array);
    }

    static function required_without($array)
    {
        return __FUNCTION__ . ":" . implode(",", (array)$array);
    }

    static function required_without_all($array)
    {
        return __FUNCTION__ . ":" . implode(",", (array)$array);
    }

    static function same(string $field)
    {
        return __FUNCTION__ . ":" . $field;
    }

    static function size(int $size)
    {
        return __FUNCTION__ . ":" . $size;
    }

    static function string()
    {
        return __FUNCTION__;
    }

    static function unique(string $table,?string $column=null,?string $exclude=null)
    {
        return __FUNCTION__;
    }
}
