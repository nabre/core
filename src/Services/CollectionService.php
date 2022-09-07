<?php

namespace Nabre\Services;

use Nabre\Models\Collection as Model;
use Nabre\Models\Collection;

class CollectionService
{
    static function checkExists()
    {
        $notRemove = Model::get()->existClass('class')->pluck('_id')->values()->toArray();
        Model::whereNotIn('_id', $notRemove)->delete();
    }

    static function syncField($collection)
    {
        if (is_null($collection->class)) {
            return;
        }

        $model = new $collection->class;
        $fields = collect([]);

        $add = $model->getFillable();
        $fields = $fields->merge($add);
        $add = $model->attributesList();
        $fields = $fields->merge($add);
        $add = $model->definedRelations()->pluck('name')->toArray();
        $fields = $fields->merge($add);

        $arrayToSave = $fields->unique()->sort()->values()->map(function ($name) use (&$collection) {
            $coll = $collection->id;
            $item = $collection->fields()->where('name', $name)->first();

            if (is_null($item)) {
                $item = $collection->fields()->create();
            }
            return $item->recursiveSave(compact('name', 'coll'))->toArray();
        })->toArray();
        $collection=$collection->recursiveSave(['fields' => $arrayToSave]);
    }

    static function getString($collection, $variable)
    {
        $items = collect(explode(".", $variable))->reverse();
        $name = $items->first();

        $items->skip(1)->reverse()->each(function ($name) use (&$collection) {
            if (!is_null($collection)) {
                $class = optional($collection->reletionshipFind($name))->model;
                $collection = is_null($class) ? null : new $class;
            }
        });

        $class = is_null($collection) ? null : get_class($collection);

        if(!is_null($class)){
            $fields=Collection::firstOrCreate(compact('class'), compact('class'))->fields()->get();
            $string=optional($fields->where('name', $name)->first())->string;
        }

        return  $string ?? $variable;
    }
}
