<?php
if (!function_exists('darkMode')) {
    function darkMode(&$darkMode)
    {
        $darkMode = ($darkMode ?? null) === true ? 'bg-dark text-light' : '';
        return $darkMode;
    }
}
if (!function_exists('multi_explode')) {
    function multi_explode(array $delimiter, $string)
    {
        $d = array_shift($delimiter);
        if ($d != NULL) {
            $tmp = explode($d, $string);
            foreach ($tmp as $key => $o) {
                $out[$key] = multi_explode($delimiter, $o);
            }
        } else {
            return $string;
        }
        return $out;
    }
}
