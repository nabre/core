<?php

namespace Nabre\Services;

class AutoSync
{
    var $model;
    var $keyName;
    var $eloquent;
    var $items;

    function __construct()
    {
        $this->eloquent = new $this->model;
        $this->keyName = $this->eloquent->getKeyName();
        $this->sync();
    }

    function sync()
    {
        $notRemove = collect([]);
        $this->exists()->each(function ($data) use (&$notRemove) {
            $item = $this->put($data);
            $item = $item->recursiveSave($data);
            $notRemove = $notRemove->push($item->{$this->keyName});
        });

        $this->current()->whereNotIn($this->keyName, $notRemove->toArray())->delete();
    }

    function current()
    {
        return $this->eloquent->all();
    }
}
