<?php

namespace Nabre\Database\Eloquent;

trait RecursiveSaveTrait
{

    function readValue($name)
    {
        $value = $this;
        collect(explode(".", $name))->each(function ($v) use (&$value) {
            if (!is_null($value)) {
                if (in_array($v, $this->getFillable())) {
                    $value = $value->getRawOriginal($v);
                } elseif (!is_null($rel = $this->relationshipFind($v))) {
                    $value = $value->$v;
                    switch ($rel->type) {
                        case "BelongsTo":
                        case "HasOne":
                            $value = optional($value)->id;
                            break;
                        case "HasMany":
                        case "BelongsToMany":
                            $value = optional($value)->modelKeys();
                            break;
                    }
                } else {
                    $value = $value->$v;
                }
            }
        });

        return $value;
    }

    function toArray($attributes = false)
    {
        $names = collect($this->getFillable())
            ->merge($this->definedRelations()->pluck('name'))
            ->push($this->getKeyName());

        if ($attributes) {
            $names = $names->merge($this->attributesList());
        }

        return $names->unique()
            ->map(function ($name) {
                $value = $this->readValue($name);
                return get_defined_vars();
            })->pluck('value', 'name')->toArray();
    }

    function recursiveSaveQuietly(array $data, $syncBool = true)
    {
        return $this->recursiveSave($data, $syncBool, true);
    }

    private function nestedSave($model, $data, $syncBool, $saveQuietly)
    {
        return (is_array($data) && isAssoc((array) $data)) ?
            data_get(
                ($model::find(data_get($data, ($collection = new $model)->getKeyName()))
                    ??
                    $model::make())->recursiveSave($data, $syncBool, $saveQuietly),
                $collection->getKeyName()
            )
            : $data;
    }

    function recursiveSave(array $data, $syncBool = true, $saveQuietly = false)
    {
        $relations = $this->definedRelations();
        $items = $relations->whereIn('name', array_keys($data));
        if ($items->count()) {
            $this->makeSave($saveQuietly);
        }

        $items->map(fn ($i) => data_set($i, 'value', data_get($data, data_get($i, 'name'))))
            ->each(function ($rel) use ($syncBool, $saveQuietly) {
                $name = data_get($rel, 'name');
                $type = data_get($rel, 'type');
                $model = data_get($rel, 'model');
                $collection = new $model;
                $data = data_get($rel, 'value');

                $cont = $this->$name();

                switch ($type) {
                    case 'BelongsTo':
                    case 'HasOne':
                        $ids = $this->nestedSave($model, $data, $syncBool, $saveQuietly);
                        break;
                    case 'BelongsToMany':
                    case 'HasMany':
                        $ids = collect((array)$data)
                            ->map(function ($d) use ($model, $syncBool, $saveQuietly) {
                                return $this->nestedSave($model, $d, $syncBool, $saveQuietly);
                            })
                            ->toArray();
                        break;
                }

                $instance = is_null($ids ?? null) ? null : $model::whereIn($collection->getKeyName(), (array) $ids)->get();

                switch ($type) {
                    case 'BelongsTo':
                        $cont->dissociate();
                        if (!is_null($instance)) {
                            $cont->associate(data_get($instance->first(), $collection->getKeyName()));
                        }
                        break;
                    case 'BelongsToMany':
                        if (!is_null($instance)) {
                            $cont->sync($instance->modelKeys(), $syncBool);
                        }
                        break;
                    case 'HasOne':
                        $fk = data_get($rel, 'foreignKey');
                        $cont->unset($fk);
                        if (!is_null($instance)) {
                            $instance = $instance->first();
                        }
                        if (!is_null($instance)) {
                            $cont->save($instance);
                        }
                        break;
                    case 'HasMany':
                        $fk = data_get($rel, 'foreignKey');
                        foreach ($cont->get() as $a) {
                            $a->unset($fk);
                        }
                        if (!is_null($instance)) {
                            $cont->saveMany($instance);
                        }
                        break;
                    case 'EmbedsOne':
                        if (is_null($data) || !count((array)$data)) {
                            $cont->unset($name);
                        } else {
                            if (is_null($embedModel = $this->$name)) {
                                $embedModel = $cont->create();
                            }
                            $embedModel->recursiveSave($data, $syncBool, $saveQuietly);
                        }
                        break;
                    case 'EmbedsMany':
                        if (is_null($data) || !count((array)$data)) {
                            $cont->unset($name);
                        } else {
                            $idName = $collection->getKeyName();
                            $ids = collect((array)$data)->map(function ($data) use ($idName, $cont) {
                                $id = data_get($data, $idName);
                                $cont = $cont->where($idName, $id)->first() ?? $cont->create();
                                return data_get($cont->recursiveSave($data), $idName);
                            })->toArray();
                            $cont->whereNotIn($idName, $ids)->each->delete();
                        }
                        break;
                }
            });

        $data = collect($data)
            ->reject(fn ($v, $k) => in_array($k, $relations->pluck('name')->toArray()))
            ->reject(fn ($v, $k) => in_array($k, $this->attributesList()))
            ->map(function ($val, $key) {
                $type = data_get($this->casts, $key);
                switch ($type) {
                    case "array":
                        $val = array_values(array_filter((array)$val, 'strlen'));
                        break;
                    case "boolean":
                        $val = (bool)$val;
                        break;
                    case "integer":
                        $val = (int)$val;
                        break;
                    case "object":
                        $val = (object)$val;
                        break;
                    case "string":
                        $val = (string)$val;
                        break;
                }
                return $val;
            })->toArray();

        $this->fill($data);

        $this->makeSave($saveQuietly);

        return $this;
    }

    function makeSave($saveQuietly)
    {
        if ($saveQuietly) {
            $this->saveQuietly();
        } else {
            $this->save();
        }
    }

    protected function findData($data, array $find)
    {
        $varName = array_intersect(array_keys($data->toArray()), $find);
        return  $data->filter(function ($val, $key) use ($varName) {
            return in_array($key, $varName);
        });
    }
}
