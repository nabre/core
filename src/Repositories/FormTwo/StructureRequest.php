<?php

namespace Nabre\Repositories\FormTwo;

trait StructureRequest
{
    private $method = null;

    static $create = 'post';
    static $update = 'put';

    function required($method = null)
    {
        $this->request("required", $method);
        return $this;
    }

    function requestCreate($string)
    {
        $this->request($string, self::$create);
        return $this;
    }

    function requestUpdate($string)
    {
        $this->request($string, self::$update);
        return $this;
    }

    function request($string, $method = null)
    {
        $this->checkMethods($method);
        $string = explode("|", implode("|", (array)$string));
        collect($method)->each(function ($method) use ($string) {
            $value = collect((array)  $this->getItemData('set.request.' . $method))->merge($string)->unique()->filter()->values()->toArray();
            $this->setItemData('set.request.' . $method, $value,true);
        });
        return $this;
    }

    protected function checkMethods(&$method)
    {
        if (is_null($method)) {
            $method = [self::$update, self::$create];
        }
        $method = array_values(array_intersect([self::$update, self::$create], (array) $method));
    }

    private function methodForm()
    {
        if (is_null(data_get($this->data, 'id'))) {
            $this->method = self::$create;
        } else {
            $this->method = self::$update;
        }

        return $this;
    }

}
