<?php
if (!function_exists('array_undot')) {
    function array_undot($arrayDot)
    {
        $array = [];
        foreach ($arrayDot as $key => $value) {
            array_set($array, $key, $value);
        }
        return $array;
    }
}

if (!function_exists('array_set')) {

    function array_set(&$array, $key, $value)
    {
        if (is_null($key)) return $array = $value;

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = array();
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }
}
