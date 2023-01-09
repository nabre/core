<?php

namespace Nabre\Repositories\Form;

use Nabre\Repositories\Form\Field;

class Structure
{
    use FormSetQuery;

    var $data;
    var $collection = null;
    var $model;
    var $elements;
    var $item;

    function __construct($data = null)
    {
        $this->data($data);
        $this->elements = collect([]);
        $this->generate();
    }

    function collection($collection)
    {
        $this->collection = $collection;
        $this->data();
        return $this;
    }

    function data($data = null)
    {
        if (!is_null($data)) {

            if (is_object($data)) {
                $this->collection = get_class($data);
            } else {
                $data = collect((array)$data);
            }
            $this->data = $data;
        } elseif (!is_null($this->collection)) {
            $this->data = new $this->collection;
        }

        return $this;
    }

    function generate()
    {
        if (!is_null($this->collection)) {
            $this->model = new $this->collection;
        }

        $this->elements = collect([]);
        $this->build();

        $this->lastInsert();

        return $this;
    }

    function lastInsert()
    {
        $this->insert();
        $this->elements = $this->elements->reject(function ($i) {
            return $i['type'] === false;
        })->values();
        return $this;
    }

    function build()
    {
    }

    function add($variable, $output = null, $label = null, $type = true)
    {
        $this->insert()->item($variable, $output, $label, $type);
        return $this;
    }

    function addValue($variable,$value=null){
        $output=Field::HIDDEN;
        $this->insert()->item($variable, $output, null, true);
        return $this;
    }

    private function insert()
    {
        if (!is_null($this->item ?? null)) {
            $this->defaultSettings();
            $this->elements = $this->elements->push($this->item);
        }
        $this->item = null;
        return $this;
    }

    private function item($variable, $output, $label, $type = true)
    {
        $model = $this->model;
        $str = $cast = null;
        $set = [];

        if ($type === true) {
            $newVariable = [];
            collect(explode(".", $variable))->each(function ($v) use (&$model, &$type, &$newVariable, &$str, &$cast, &$set) {
                if ($type === true || $type == 'relation') {
                    $newVariable[] = $str = $v;

                    if (!is_null($model ?? null)) {
                        if ($this->isRelation($v, $model, $rel)) {
                            $type = 'relation';
                            $set['rel'] = $rel;
                        } else
                        if ($this->isAttribute($v, $model)) {
                            $type = 'attribute';
                        } else
                        if ($this->isFillable($v, $model)) {
                            $type = 'fillable';
                            $cast = $model->getCasts()[$v] ?? null;
                        } else {
                            $type = false;
                        }
                    }
                }
            });

            $newVariable = implode(".", $newVariable);
            if ($newVariable != $variable) {
                $type = false;
            }
        }

        unset($newVariable);
        $this->item = array_filter(get_defined_vars(), fn ($item) => (null !== $item && count((array)$item)));
        return $this;
    }

    private function isFillable($v, $model)
    {
        return in_array($v, $model->getFillable());
    }

    private function isAttribute($v, $model)
    {
        return method_exists($model, 'get' . str_replace("_", "", $v) . 'attribute');
    }

    private function isRelation($v, &$model, &$rel)
    {
        $rel = $model->relationshipFind($v);

        #Conrollo bolonstTo,HasOne

        $bool = (bool) !is_null($rel);
        $model = $bool ? new $rel->model : $model;
        return $bool;
    }
}
